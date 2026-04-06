/**
 * Linter Runner Utility
 * 
 * Execute code style and static analysis tools:
 * - Laravel Pint (PSR-12)
 * - PHPStan (static analysis)
 * - Blade linting
 */

import { execSync } from 'child_process';
import { readFile, fileExists } from '../shared/file-operations';

interface LintResult {
  passed: boolean;
  errors: LintError[];
  warnings: LintWarning[];
  fixed: string[];
  duration: number;
}

interface LintError {
  file: string;
  line: number;
  column: number;
  message: string;
  rule: string;
}

interface LintWarning {
  file: string;
  line: number;
  message: string;
  rule: string;
}

/**
 * Run Laravel Pint
 */
export function runPint(basePath: string, fix: boolean = false): LintResult {
  const cmd = fix
    ? './vendor/bin/pint --colors=never'
    : './vendor/bin/pint --test --colors=never';
  
  return executeLint(cmd, basePath);
}

/**
 * Run Pint on specific files
 */
export function runPintOnFiles(files: string | string[], basePath: string, fix: boolean = false): LintResult {
  const fileList = Array.isArray(files) ? files.join(' ') : files;
  const cmd = fix
    ? `./vendor/bin/pint ${fileList} --colors=never`
    : `./vendor/bin/pint --test ${fileList} --colors=never`;
  
  return executeLint(cmd, basePath);
}

/**
 * Run PHPStan static analysis
 */
export function runPhpStan(basePath: string, level: number = 5): LintResult {
  if (!fileExists(`${basePath}/phpstan.neon`)) {
    return {
      passed: true,
      errors: [],
      warnings: [],
      fixed: [],
      duration: 0,
    };
  }
  
  const cmd = `./vendor/bin/phpstan analyse --level=${level} --no-progress --error-format=raw`;
  return executeLint(cmd, basePath);
}

/**
 * Check Blade syntax
 */
export function checkBladeSyntax(filePath: string, basePath: string): LintResult {
  const result: LintResult = {
    passed: true,
    errors: [],
    warnings: [],
    fixed: [],
    duration: 0,
  };
  
  try {
    const content = readFile(filePath);
    
    // Check balanced directives
    const directives: Record<string, string> = {
      '@if': '@endif',
      '@foreach': '@endforeach',
      '@for': '@endfor',
      '@while': '@endwhile',
      '@section': '@endsection',
      '@push': '@endpush',
      '@isset': '@endisset',
      '@empty': '@endempty',
      '@auth': '@endauth',
      '@guest': '@endguest',
      '@production': '@endproduction',
    };
    
    for (const [open, close] of Object.entries(directives)) {
      const openCount = (content.match(new RegExp(open.replace('@', '@'), 'g')) || []).length;
      const closeCount = (content.match(new RegExp(close.replace('@', '@'), 'g')) || []).length;
      
      if (openCount !== closeCount) {
        result.passed = false;
        result.errors.push({
          file: filePath,
          line: 0,
          column: 0,
          message: `Unbalanced ${open}/${close}: ${openCount} opening, ${closeCount} closing`,
          rule: 'balanced-directives',
        });
      }
    }
  } catch (error: any) {
    result.passed = false;
    result.errors.push({
      file: filePath,
      line: 0,
      column: 0,
      message: error.message,
      rule: 'file-read',
    });
  }
  
  return result;
}

/**
 * Execute lint command and parse output
 */
function executeLint(cmd: string, basePath: string): LintResult {
  const result: LintResult = {
    passed: true,
    errors: [],
    warnings: [],
    fixed: [],
    duration: 0,
  };
  
  const startTime = Date.now();
  
  try {
    execSync(cmd, {
      cwd: basePath,
      encoding: 'utf-8',
      maxBuffer: 10 * 1024 * 1024,
    });
  } catch (error: any) {
    const output = error.stdout || error.stderr || '';
    
    // Parse Pint output
    const errorLines = output.split('\n').filter(line => line.includes('.php'));
    for (const line of errorLines) {
      const match = line.match(/(.+\.php):(\d+):(\d+)\s+(.*)/);
      if (match) {
        result.passed = false;
        result.errors.push({
          file: match[1],
          line: parseInt(match[2]),
          column: parseInt(match[3]),
          message: match[4],
          rule: 'pint',
        });
      }
    }
    
    // Check if files were fixed
    if (output.includes('fixed')) {
      const fixedMatch = output.match(/(\d+)\s+files?\s+fixed/);
      if (fixedMatch) {
        result.fixed.push(`~${fixedMatch[1]} files fixed`);
      }
    }
  }
  
  result.duration = (Date.now() - startTime) / 1000;
  return result;
}
