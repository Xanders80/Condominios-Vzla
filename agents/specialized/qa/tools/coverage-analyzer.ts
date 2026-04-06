/**
 * Coverage Analyzer Utility
 * 
 * Analyze PHPUnit test coverage:
 * - Parse coverage reports
 * - Identify untested files
 * - Generate coverage improvement suggestions
 * - Track coverage trends
 */

import { execSync } from 'child_process';
import { readFile, listFilesRecursive } from '../../shared/file-operations';

interface CoverageResult {
  lines: CoverageMetric;
  methods: CoverageMetric;
  functions: CoverageMetric;
  classes: CoverageMetric;
  files: FileCoverage[];
  untestedFiles: string[];
}

interface CoverageMetric {
  percent: number;
  tested: number;
  total: number;
}

interface FileCoverage {
  file: string;
  lines: CoverageMetric;
  methods: CoverageMetric;
}

/**
 * Run PHPUnit with coverage and parse results
 */
export function analyzeCoverage(basePath: string): CoverageResult {
  try {
    const output = execSync(
      'php artisan test --coverage-text --colors=never',
      {
        cwd: basePath,
        encoding: 'utf-8',
        maxBuffer: 10 * 1024 * 1024,
      }
    );

    return parseCoverageOutput(output);
  } catch (error: any) {
    const output = error.stdout || error.stderr || '';
    return parseCoverageOutput(output);
  }
}

/**
 * Parse PHPUnit coverage text output
 */
function parseCoverageOutput(output: string): CoverageResult {
  const result: CoverageResult = {
    lines: { percent: 0, tested: 0, total: 0 },
    methods: { percent: 0, tested: 0, total: 0 },
    functions: { percent: 0, tested: 0, total: 0 },
    classes: { percent: 0, tested: 0, total: 0 },
    files: [],
    untestedFiles: [],
  };

  // Parse summary: "Lines: 45.67% ( 123/270 )"
  const linesMatch = output.match(/Lines:\s+([\d.]+)%\s*\(\s*(\d+)\/(\d+)\s*\)/);
  if (linesMatch) {
    result.lines = {
      percent: parseFloat(linesMatch[1]),
      tested: parseInt(linesMatch[2]),
      total: parseInt(linesMatch[3]),
    };
  }

  const methodsMatch = output.match(/Methods:\s+([\d.]+)%\s*\(\s*(\d+)\/(\d+)\s*\)/);
  if (methodsMatch) {
    result.methods = {
      percent: parseFloat(methodsMatch[1]),
      tested: parseInt(methodsMatch[2]),
      total: parseInt(methodsMatch[3]),
    };
  }

  const functionsMatch = output.match(/Functions:\s+([\d.]+)%\s*\(\s*(\d+)\/(\d+)\s*\)/);
  if (functionsMatch) {
    result.functions = {
      percent: parseFloat(functionsMatch[1]),
      tested: parseInt(functionsMatch[2]),
      total: parseInt(functionsMatch[3]),
    };
  }

  const classesMatch = output.match(/Classes:\s+([\d.]+)%\s*\(\s*(\d+)\/(\d+)\s*\)/);
  if (classesMatch) {
    result.classes = {
      percent: parseFloat(classesMatch[1]),
      tested: parseInt(classesMatch[2]),
      total: parseInt(classesMatch[3]),
    };
  }

  return result;
}

/**
 * Find files without tests
 */
export function findUntestedFiles(basePath: string): string[] {
  const appFiles = listFilesRecursive(`${basePath}/app`, '.php')
    .filter(f => !f.includes('/Providers/') && !f.includes('/Console/') && !f.includes('/Exceptions/'));

  const testFiles = listFilesRecursive(`${basePath}/tests`, '.php');

  const testedModules: string[] = [];
  for (const testFile of testFiles) {
    const content = readFile(testFile);
    const useMatches = content.matchAll(/use\s+App\\([\w\\]+);/g);
    for (const match of useMatches) {
      testedModules.push(match[1]);
    }
  }

  const untested: string[] = [];
  for (const appFile of appFiles) {
    const relativePath = appFile.replace(`${basePath}/app/`, '');
    const className = relativePath.replace(/\//g, '\\').replace('.php', '');

    if (!testedModules.includes(className)) {
      untested.push(relativePath);
    }
  }

  return untested;
}

/**
 * Generate coverage improvement report
 */
export function generateCoverageReport(basePath: string): string {
  const coverage = analyzeCoverage(basePath);
  const untested = findUntestedFiles(basePath);

  let report = '# Test Coverage Report\n\n';
  report += `## Summary\n\n`;
  report += `| Metric | Coverage |\n`;
  report += `|--------|----------|\n`;
  report += `| Lines | ${coverage.lines.percent.toFixed(1)}% (${coverage.lines.tested}/${coverage.lines.total}) |\n`;
  report += `| Methods | ${coverage.methods.percent.toFixed(1)}% (${coverage.methods.tested}/${coverage.methods.total}) |\n`;
  report += `| Classes | ${coverage.classes.percent.toFixed(1)}% (${coverage.classes.tested}/${coverage.classes.total}) |\n\n`;

  if (untested.length > 0) {
    report += `## Untested Files (${untested.length})\n\n`;
    for (const file of untested.slice(0, 20)) {
      report += `- ${file}\n`;
    }
    if (untested.length > 20) {
      report += `- ... and ${untested.length - 20} more\n`;
    }
  }

  report += `\n## Recommendations\n\n`;

  if (coverage.lines.percent < 50) {
    report += '- CRITICAL: Line coverage below 50%. Prioritize writing tests for core modules.\n';
  } else if (coverage.lines.percent < 70) {
    report += '- WARNING: Line coverage below 70%. Add tests for untested modules.\n';
  } else if (coverage.lines.percent < 90) {
    report += '- INFO: Line coverage is acceptable but could be improved.\n';
  }

  if (untested.length > 10) {
    report += `- ${untested.length} files have no tests. Start with high-priority modules (payments, receipts, debts).\n`;
  }

  return report;
}
