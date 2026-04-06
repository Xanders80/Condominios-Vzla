/**
 * Swagger Generator Utility
 * 
 * Generate/update Swagger/OpenAPI annotations:
 * - Add annotations to existing controllers
 * - Generate full API documentation
 * - Update storage/api-docs/api-docs.json
 */

import { readFile, writeFile, searchInFiles } from '../../shared/file-operations';

interface SwaggerConfig {
  basePath: string;
  apiVersion: string;
  title: string;
  description: string;
  version: string;
}

interface EndpointDoc {
  method: string;
  path: string;
  summary: string;
  tags: string[];
  security: boolean;
  parameters: ParamDoc[];
  requestBody?: RequestBodyDoc;
  responses: ResponseDoc[];
}

interface ParamDoc {
  name: string;
  in: 'path' | 'query' | 'header';
  required: boolean;
  type: string;
}

interface RequestBodyDoc {
  schema: Record<string, string>;
  required: string[];
}

interface ResponseDoc {
  status: number;
  description: string;
  schema?: Record<string, unknown>;
}

/**
 * Generate Swagger annotations for a controller
 */
export function generateControllerAnnotations(basePath: string, config: SwaggerConfig, endpoints: EndpointDoc[]): string {
  return endpoints.map(ep => generateEndpointAnnotation(ep)).join('\n\n');
}

function generateEndpointAnnotation(ep: EndpointDoc): string {
  const oaMethod = ep.method.toUpperCase();
  const security = ep.security ? '\n     *     security={{"sanctum":{}}},' : '';
  
  const parameters = ep.parameters.map(p => 
    `\n     *     @OA\\Parameter(name="${p.name}", in="${p.in}", required=${p.required}, @OA\\Schema(type="${p.type}"))`
  ).join(',');
  
  const responses = ep.responses.map(r => 
    `\n     *     @OA\\Response(response=${r.status}, description="${r.description}")`
  ).join(',');
  
  return `    /**
     * @OA\\${oaMethod}(
     *     path="/api/${ep.path}",
     *     tags={${ep.tags.map(t => `"${t}"`).join(', ')}},
     *     summary="${ep.summary}",${security}${parameters}${responses}
     * )
     */`;
}

/**
 * Scan controllers for missing Swagger annotations
 */
export function findMissingAnnotations(basePath: string, apiVersion: string): MissingAnnotation[] {
  const missing: MissingAnnotation[] = [];
  const controllersPath = `${basePath}/app/Http/Controllers/Api/${apiVersion}`;
  
  try {
    const { listFilesRecursive } = require('../../shared/file-operations');
    const controllerFiles = listFilesRecursive(controllersPath, '.php');
    
    for (const file of controllerFiles) {
      const content = readFile(file);
      const methods = content.matchAll(/public\s+function\s+(\w+)\s*\(/g);
      
      for (const match of methods) {
        const methodName = match[1];
        if (!['__construct', 'middleware'].includes(methodName)) {
          const beforeMethod = content.substring(0, match.index);
          if (!beforeMethod.trimEnd().endsWith('*/')) {
            missing.push({
              file: file.replace(basePath + '/', ''),
              method: methodName,
              annotation: `${methodName} is missing Swagger annotation`,
            });
          }
        }
      }
    }
  } catch {
    // Controllers path may not exist yet
  }
  
  return missing;
}

interface MissingAnnotation {
  file: string;
  method: string;
  annotation: string;
}
