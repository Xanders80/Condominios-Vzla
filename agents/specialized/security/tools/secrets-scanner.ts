/**
 * Secrets Scanner Utility
 * 
 * Scan codebase for accidentally committed secrets:
 * - API keys
 * - Passwords
 * - Private keys
 * - Database credentials
 * - Tokens
 */

import { listFilesRecursive, readFile } from '../../shared/file-operations';

interface SecretFinding {
  file: string;
  line: number;
  type: string;
  matched: string;
  severity: 'critical' | 'high' | 'medium' | 'low';
}

interface ScanResult {
  totalFilesScanned: number;
  findings: SecretFinding[];
  criticalCount: number;
  highCount: number;
}

/**
 * Patterns that indicate secrets
 */
const SECRET_PATTERNS: { pattern: RegExp; type: string; severity: SecretFinding['severity'] }[] = [
  { pattern: /(?:api[_-]?key|apikey)\s*[:=]\s*['"]([a-zA-Z0-9]{20,})['"]/gi, type: 'API Key', severity: 'critical' },
  { pattern: /(?:secret[_-]?key|secret)\s*[:=]\s*['"]([a-zA-Z0-9]{20,})['"]/gi, type: 'Secret Key', severity: 'critical' },
  { pattern: /(?:password|passwd|pwd)\s*[:=]\s*['"]([^'"]{8,})['"]/gi, type: 'Password', severity: 'critical' },
  { pattern: /(?:private[_-]?key)\s*[:=]\s*['"]([^'"]+)['"]/gi, type: 'Private Key', severity: 'critical' },
  { pattern: /(?:aws[_-]?access[_-]?key[_-]?id)\s*[:=]\s*['"]([A-Z0-9]{20})['"]/gi, type: 'AWS Access Key', severity: 'critical' },
  { pattern: /(?:aws[_-]?secret[_-]?access[_-]?key)\s*[:=]\s*['"]([a-zA-Z0-9/+=]{40})['"]/gi, type: 'AWS Secret Key', severity: 'critical' },
  { pattern: /(?:stripe[_-]?secret)\s*[:=]\s*['"]sk_[a-zA-Z0-9]{24,}['"]/gi, type: 'Stripe Secret Key', severity: 'critical' },
  { pattern: /(?:DB[_-]?PASSWORD)\s*[:=]\s*['"]([^'"]+)['"]/gi, type: 'Database Password', severity: 'critical' },
  { pattern: /-----BEGIN (?:RSA |EC |DSA )?PRIVATE KEY-----/gi, type: 'Private Key File', severity: 'critical' },
  { pattern: /(?:token|auth[_-]?token|access[_-]?token)\s*[:=]\s*['"]([a-zA-Z0-9]{20,})['"]/gi, type: 'Token', severity: 'high' },
  { pattern: /(?:bearer)\s+([a-zA-Z0-9\-._~+/]{20,})/gi, type: 'Bearer Token', severity: 'high' },
];

/**
 * Files to always skip
 */
const SKIP_PATTERNS = [
  'node_modules/',
  'vendor/',
  '.git/',
  'storage/',
  'bootstrap/cache/',
  '.env',
  '.env.example',
  '*.lock',
  '*.min.js',
  '*.min.css',
];

/**
 * Scan the codebase for secrets
 */
export function scanForSecrets(basePath: string): ScanResult {
  const result: ScanResult = {
    totalFilesScanned: 0,
    findings: [],
    criticalCount: 0,
    highCount: 0,
  };

  const allFiles = listFilesRecursive(basePath);

  for (const file of allFiles) {
    const relativePath = file.replace(basePath + '/', '');

    // Skip excluded files
    if (SKIP_PATTERNS.some(pattern => relativePath.includes(pattern))) {
      continue;
    }

    // Only scan text-based files
    const ext = relativePath.split('.').pop()?.toLowerCase();
    if (!['php', 'js', 'ts', 'json', 'yaml', 'yml', 'env', 'md', 'txt', 'blade.php'].includes(ext || '')) {
      continue;
    }

    result.totalFilesScanned++;

    try {
      const content = readFile(file);
      const lines = content.split('\n');

      for (let lineNum = 0; lineNum < lines.length; lineNum++) {
        const line = lines[lineNum];

        for (const { pattern, type, severity } of SECRET_PATTERNS) {
          pattern.lastIndex = 0;
          const match = pattern.exec(line);

          if (match) {
            const finding: SecretFinding = {
              file: relativePath,
              line: lineNum + 1,
              type,
              matched: match[0].substring(0, 50) + '...',
              severity,
            };

            result.findings.push(finding);

            if (severity === 'critical') result.criticalCount++;
            if (severity === 'high') result.highCount++;
          }
        }
      }
    } catch {
      // Skip binary or unreadable files
    }
  }

  return result;
}

/**
 * Generate secrets scan report
 */
export function generateSecretsReport(basePath: string): string {
  const result = scanForSecrets(basePath);

  let report = '# Secrets Scan Report\n\n';
  report += `Files scanned: ${result.totalFilesScanned}\n`;
  report += `Total findings: ${result.findings.length}\n`;
  report += `Critical: ${result.criticalCount}\n`;
  report += `High: ${result.highCount}\n\n`;

  if (result.findings.length > 0) {
    report += '## Findings\n\n';
    for (const finding of result.findings) {
      report += `- [${finding.severity.toUpperCase()}] ${finding.type} in ${finding.file}:${finding.line}\n`;
      report += `  Matched: ${finding.matched}\n\n`;
    }
  } else {
    report += 'No secrets detected.\n';
  }

  return report;
}
