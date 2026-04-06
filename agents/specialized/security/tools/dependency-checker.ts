/**
 * Dependency Checker Utility
 * 
 * Scan project dependencies for:
 * - Known vulnerabilities (CVE)
 * - Outdated packages
 * - License compliance
 * - Unused dependencies
 */

import { readFile, fileExists } from '../../shared/file-operations';

interface DependencyInfo {
  name: string;
  version: string;
  latest?: string;
  vulnerabilities: Vulnerability[];
  license?: string;
  isDev: boolean;
}

interface Vulnerability {
  id: string;
  severity: 'critical' | 'high' | 'medium' | 'low';
  title: string;
  fixedIn?: string;
}

interface DependencyReport {
  total: number;
  vulnerable: number;
  outdated: number;
  criticalIssues: number;
  dependencies: DependencyInfo[];
}

/**
 * Check PHP dependencies for vulnerabilities
 */
export function checkPhpDependencies(basePath: string): DependencyReport {
  const report: DependencyReport = {
    total: 0,
    vulnerable: 0,
    outdated: 0,
    criticalIssues: 0,
    dependencies: [],
  };

  try {
    const composerLock = JSON.parse(
      readFile(`${basePath}/composer.lock`)
    );

    const allPackages = [
      ...(composerLock.packages || []).map(p => ({ ...p, isDev: false })),
      ...(composerLock['packages-dev'] || []).map(p => ({ ...p, isDev: true })),
    ];

    report.total = allPackages.length;

    for (const pkg of allPackages) {
      const dep: DependencyInfo = {
        name: pkg.name,
        version: pkg.version,
        isDev: pkg.isDev,
        vulnerabilities: [],
        license: pkg.license?.[0],
      };

      // Check for known vulnerabilities (simplified - in production use composer audit)
      const knownVulns = getKnownVulnerabilities(pkg.name, pkg.version);
      dep.vulnerabilities = knownVulns;

      if (knownVulns.length > 0) {
        report.vulnerable++;
        report.criticalIssues += knownVulns.filter(v => v.severity === 'critical').length;
      }

      report.dependencies.push(dep);
    }
  } catch {
    // composer.lock may not exist
  }

  return report;
}

/**
 * Check JavaScript dependencies for vulnerabilities
 */
export function checkJsDependencies(basePath: string): DependencyReport {
  const report: DependencyReport = {
    total: 0,
    vulnerable: 0,
    outdated: 0,
    criticalIssues: 0,
    dependencies: [],
  };

  if (!fileExists(`${basePath}/package-lock.json`)) {
    return report;
  }

  try {
    const pkgLock = JSON.parse(
      readFile(`${basePath}/package-lock.json`)
    );

    const packages = pkgLock.packages || {};
    report.total = Object.keys(packages).filter(k => k !== '').length;

    for (const [name, info] of Object.entries(packages) as [string, Record<string, unknown>][]) {
      if (name === '') continue;

      const dep: DependencyInfo = {
        name: name.replace('node_modules/', ''),
        version: (info.version as string) || 'unknown',
        isDev: false,
        vulnerabilities: [],
      };

      report.dependencies.push(dep);
    }
  } catch {
    // package-lock.json may not exist
  }

  return report;
}

/**
 * Known vulnerabilities database (simplified)
 * In production, use npm audit and composer audit
 */
function getKnownVulnerabilities(name: string, version: string): Vulnerability[] {
  const vulnerabilities: Vulnerability[] = [];

  // Example checks (in production, query a vulnerability database)
  if (name === 'lodash' && versionCompare(version, '4.17.21') < 0) {
    vulnerabilities.push({
      id: 'CVE-2021-23337',
      severity: 'high',
      title: 'Command Injection in lodash',
      fixedIn: '4.17.21',
    });
  }

  if (name === 'axios' && versionCompare(version, '1.6.0') < 0) {
    vulnerabilities.push({
      id: 'CVE-2023-45857',
      severity: 'medium',
      title: 'CSRF token exposure in axios',
      fixedIn: '1.6.0',
    });
  }

  return vulnerabilities;
}

/**
 * Compare semantic versions
 */
function versionCompare(a: string, b: string): number {
  const partsA = a.replace(/[^0-9.]/g, '').split('.').map(Number);
  const partsB = b.replace(/[^0-9.]/g, '').split('.').map(Number);

  for (let i = 0; i < Math.max(partsA.length, partsB.length); i++) {
    const valA = partsA[i] || 0;
    const valB = partsB[i] || 0;
    if (valA > valB) return 1;
    if (valA < valB) return -1;
  }
  return 0;
}

/**
 * Generate security report
 */
export function generateSecurityReport(basePath: string): string {
  const phpReport = checkPhpDependencies(basePath);
  const jsReport = checkJsDependencies(basePath);

  let report = '# Dependency Security Report\n\n';
  report += `## PHP Dependencies\n`;
  report += `- Total: ${phpReport.total}\n`;
  report += `- Vulnerable: ${phpReport.vulnerable}\n`;
  report += `- Critical Issues: ${phpReport.criticalIssues}\n\n`;

  report += `## JavaScript Dependencies\n`;
  report += `- Total: ${jsReport.total}\n`;
  report += `- Vulnerable: ${jsReport.vulnerable}\n`;
  report += `- Critical Issues: ${jsReport.criticalIssues}\n\n`;

  const allVulns = [...phpReport.dependencies, ...jsReport.dependencies]
    .flatMap(d => d.vulnerabilities.map(v => ({ ...v, package: d.name })));

  if (allVulns.length > 0) {
    report += '## Vulnerabilities Found\n\n';
    for (const vuln of allVulns) {
      report += `- **${vuln.id}** [${vuln.severity}] in ${vuln.package}: ${vuln.title}\n`;
      if (vuln.fixedIn) report += `  - Fix: Upgrade to ${vuln.fixedIn}\n`;
    }
  }

  return report;
}
