/**
 * Search Engine Utility
 * 
 * Search through the codebase for patterns, definitions, and references:
 * - Find class definitions
 * - Find method usages
 * - Find route definitions
 * - Find view references
 */

import { searchInFiles, listFilesRecursive, readFile } from './file-operations';

/**
 * Find all Laravel route definitions
 */
export function findRoutes(basePath: string): RouteDefinition[] {
  const routeFiles = ['routes/web.php', 'routes/api.php', 'routes/backend.php', 'routes/mvc-route.php'];
  const routes: RouteDefinition[] = [];
  
  for (const routeFile of routeFiles) {
    try {
      const content = readFile(`${basePath}/${routeFile}`);
      const routeRegex = /Route::(get|post|put|patch|delete|resource|prefix)\s*\(\s*['"]([^'"]+)['"]/g;
      let match;
      
      while ((match = routeRegex.exec(content)) !== null) {
        routes.push({
          method: match[1],
          path: match[2],
          file: routeFile,
          line: content.substring(0, match.index).split('\n').length,
        });
      }
    } catch {
      // Route file doesn't exist
    }
  }
  
  return routes;
}

/**
 * Find all Eloquent model definitions
 */
export function findModels(basePath: string): ModelDefinition[] {
  const modelsPath = `${basePath}/app/Models`;
  const modelFiles = listFilesRecursive(modelsPath, '.php');
  
  return modelFiles.map(file => {
    const content = readFile(file);
    const classMatch = content.match(/class\s+(\w+)\s+extends\s+Model/);
    const fillableMatch = content.match(/\$fillable\s*=\s*\[([\s\S]*?)\]/);
    
    return {
      name: classMatch ? classMatch[1] : 'Unknown',
      file: file.replace(basePath + '/', ''),
      fillable: fillableMatch
        ? [...fillableMatch[1].matchAll(/['"](\w+)['"]/g)].map(m => m[1])
        : [],
    };
  });
}

/**
 * Find all controller action methods
 */
export function findControllerActions(basePath: string): ControllerAction[] {
  const controllersPath = `${basePath}/app/Http/Controllers`;
  const controllerFiles = listFilesRecursive(controllersPath, '.php');
  const actions: ControllerAction[] = [];
  
  for (const file of controllerFiles) {
    const content = readFile(file);
    const classMatch = content.match(/class\s+(\w+)/);
    const methodRegex = /public\s+function\s+(\w+)\s*\(/g;
    let match;
    
    while ((match = methodRegex.exec(content)) !== null) {
      actions.push({
        controller: classMatch ? classMatch[1] : 'Unknown',
        action: match[1],
        file: file.replace(basePath + '/', ''),
        line: content.substring(0, match.index).split('\n').length,
      });
    }
  }
  
  return actions;
}

/**
 * Find where a model relationship is used
 */
export function findRelationshipUsage(basePath: string, relationship: string): UsageReference[] {
  return searchInFiles(basePath, new RegExp(`->${relationship}\\b`), '.php')
    .map(result => ({
      file: result.file,
      line: result.line,
      content: result.content,
    }));
}

/**
 * Find all Blade view references to a route
 */
export function findRouteReferences(basePath: string, routeName: string): UsageReference[] {
  return searchInFiles(`${basePath}/resources/views`, new RegExp(`route\\(['"]${routeName}['"]`), '.blade.php')
    .map(result => ({
      file: result.file,
      line: result.line,
      content: result.content,
    }));
}

export interface RouteDefinition {
  method: string;
  path: string;
  file: string;
  line: number;
}

export interface ModelDefinition {
  name: string;
  file: string;
  fillable: string[];
}

export interface ControllerAction {
  controller: string;
  action: string;
  file: string;
  line: number;
}

export interface UsageReference {
  file: string;
  line: number;
  content: string;
}
