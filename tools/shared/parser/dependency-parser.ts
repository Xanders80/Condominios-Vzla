/**
 * Dependency Parser Utility
 * 
 * Parse composer.json and package.json to extract:
 * - Dependencies and versions
 * - Dev dependencies
 * - Version constraints
 * - Potential conflicts
 */

import { readFile } from './file-operations';

interface ComposerJson {
  require: Record<string, string>;
  'require-dev': Record<string, string>;
  autoload: { 'psr-4': Record<string, string> };
  scripts: Record<string, string | string[]>;
  config: Record<string, unknown>;
}

interface PackageJson {
  dependencies: Record<string, string>;
  devDependencies: Record<string, string>;
  scripts: Record<string, string>;
}

/**
 * Parse composer.json
 */
export function parseComposerJson(basePath: string): ComposerJson {
  const content = readFile(`${basePath}/composer.json`);
  return JSON.parse(content);
}

/**
 * Parse package.json
 */
export function parsePackageJson(basePath: string): PackageJson {
  const content = readFile(`${basePath}/package.json`);
  return JSON.parse(content);
}

/**
 * Get all PHP dependencies with their versions
 */
export function getPhpDependencies(basePath: string): Dependency[] {
  const composer = parseComposerJson(basePath);
  const deps: Dependency[] = [];
  
  for (const [name, version] of Object.entries(composer.require || {})) {
    deps.push({ name, version, type: 'production', ecosystem: 'php' });
  }
  
  for (const [name, version] of Object.entries(composer['require-dev'] || {})) {
    deps.push({ name, version, type: 'development', ecosystem: 'php' });
  }
  
  return deps;
}

/**
 * Get all JS dependencies with their versions
 */
export function getJsDependencies(basePath: string): Dependency[] {
  const pkg = parsePackageJson(basePath);
  const deps: Dependency[] = [];
  
  for (const [name, version] of Object.entries(pkg.dependencies || {})) {
    deps.push({ name, version, type: 'production', ecosystem: 'javascript' });
  }
  
  for (const [name, version] of Object.entries(pkg.devDependencies || {})) {
    deps.push({ name, version, type: 'development', ecosystem: 'javascript' });
  }
  
  return deps;
}

/**
 * Check for outdated or vulnerable dependencies
 * (Simplified - in production, would use npm audit / composer audit)
 */
export function checkDependencies(basePath: string): DependencyCheck {
  const phpDeps = getPhpDependencies(basePath);
  const jsDeps = getJsDependencies(basePath);
  
  return {
    php: {
      total: phpDeps.length,
      production: phpDeps.filter(d => d.type === 'production').length,
      development: phpDeps.filter(d => d.type === 'development').length,
    },
    javascript: {
      total: jsDeps.length,
      production: jsDeps.filter(d => d.type === 'production').length,
      development: jsDeps.filter(d => d.type === 'development').length,
    },
  };
}

/**
 * Get PSR-4 autoload mappings
 */
export function getPsr4Mappings(basePath: string): Record<string, string> {
  const composer = parseComposerJson(basePath);
  return composer.autoload?.['psr-4'] || {};
}

export interface Dependency {
  name: string;
  version: string;
  type: 'production' | 'development';
  ecosystem: 'php' | 'javascript';
}

export interface DependencyCheck {
  php: { total: number; production: number; development: number };
  javascript: { total: number; production: number; development: number };
}
