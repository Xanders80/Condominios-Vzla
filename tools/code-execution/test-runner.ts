/**
 * Test Runner Utility
 * 
 * Execute PHPUnit tests and parse results:
 * - Run all tests
 * - Run specific test file or method
 * - Parse test output for failures
 * - Generate coverage report
 */

import { execSync } from 'child_process';

interface TestResult {
  passed: number;
  failed: number;
  skipped: number;
  errors: TestError[];
  duration: number;
  coverage?: CoverageReport;
}

interface TestError {
  test: string;
  file: string;
  line: number;
  message: string;
  type: 'failure' | 'error';
}

interface CoverageReport {
  lines: number;
  methods: number;
  functions: number;
  classes: number;
}

/**
 * Run all PHPUnit tests
 */
export function runAllTests(basePath: string, coverage: boolean = false): TestResult {
  const cmd = coverage
    ? 'php artisan test --coverage-text --colors=never'
    : 'php artisan test --colors=never';
  
  return executeTest(cmd, basePath);
}

/**
 * Run a specific test file
 */
export function runTestFile(filePath: string, basePath: string): TestResult {
  const cmd = `php artisan test ${filePath} --colors=never`;
  return executeTest(cmd, basePath);
}

/**
 * Run a specific test method
 */
export function runTestMethod(testClass: string, methodName: string, basePath: string): TestResult {
  const cmd = `php artisan test --filter=${testClass}::${methodName} --colors=never`;
  return executeTest(cmd, basePath);
}

/**
 * Run tests matching a pattern
 */
export function runTestsByPattern(pattern: string, basePath: string): TestResult {
  const cmd = `php artisan test --filter=${pattern} --colors=never`;
  return executeTest(cmd, basePath);
}

/**
 * Execute test command and parse output
 */
function executeTest(cmd: string, basePath: string): TestResult {
  try {
    const output = execSync(cmd, {
      cwd: basePath,
      encoding: 'utf-8',
      maxBuffer: 10 * 1024 * 1024, // 10MB
    });
    
    return parseTestOutput(output);
  } catch (error: any) {
    // PHPUnit returns non-zero exit code on failure
    const output = error.stdout || error.stderr || '';
    const result = parseTestOutput(output);
    return result;
  }
}

/**
 * Parse PHPUnit output
 */
function parseTestOutput(output: string): TestResult {
  const result: TestResult = {
    passed: 0,
    failed: 0,
    skipped: 0,
    errors: [],
    duration: 0,
  };
  
  // Parse summary line: "Tests:    28, Assertions: 150, Failures: 2, Skipped: 1."
  const summaryMatch = output.match(/Tests:\s+(\d+).*?(?:Assertions:\s+(\d+))?.*?(?:Failures:\s+(\d+))?.*?(?:Errors:\s+(\d+))?.*?(?:Skipped:\s+(\d+))?/);
  if (summaryMatch) {
    result.passed = parseInt(summaryMatch[1]) - parseInt(summaryMatch[3] || '0') - parseInt(summaryMatch[4] || '0');
    result.failed = parseInt(summaryMatch[3] || '0');
    result.errors = [];
    result.skipped = parseInt(summaryMatch[5] || '0');
  }
  
  // Parse duration: "Time: 00:05.123"
  const timeMatch = output.match(/Time:\s+(\d+):(\d+)\.(\d+)/);
  if (timeMatch) {
    result.duration = parseInt(timeMatch[1]) * 60 + parseInt(timeMatch[2]) + parseInt(timeMatch[3]) / 1000;
  }
  
  // Parse individual failures
  const failureRegex = /(\d+)\)\s+([\w\\]+::\w+)\n\s*(.*)/g;
  let match;
  while ((match = failureRegex.exec(output)) !== null) {
    result.errors.push({
      test: match[2],
      file: '',
      line: parseInt(match[1]),
      message: match[3].trim(),
      type: 'failure',
    });
  }
  
  // Parse coverage if available
  const linesMatch = output.match(/Lines:\s+([\d.]+)%/);
  if (linesMatch) {
    result.coverage = {
      lines: parseFloat(linesMatch[1]),
      methods: 0,
      functions: 0,
      classes: 0,
    };
  }
  
  return result;
}

/**
 * Get list of test files
 */
export function getTestFiles(basePath: string): string[] {
  const { listFilesRecursive } = require('../shared/file-operations');
  return listFilesRecursive(`${basePath}/tests`, '.php');
}
