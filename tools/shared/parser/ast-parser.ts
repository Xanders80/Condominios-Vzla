/**
 * PHP AST Parser Utility
 * 
 * Parses PHP files to extract structural information:
 * - Classes and their properties/methods
 * - Method signatures with parameters and return types
 * - Use statements and dependencies
 * - DocBlock comments
 */

interface ParsedClass {
  name: string;
  namespace: string;
  extends?: string;
  implements?: string[];
  properties: Property[];
  methods: Method[];
  useStatements: string[];
  docBlock?: string;
}

interface Method {
  name: string;
  visibility: 'public' | 'protected' | 'private';
  isStatic: boolean;
  parameters: Parameter[];
  returnType?: string;
  docBlock?: string;
  body: string;
  lineStart: number;
  lineEnd: number;
}

interface Parameter {
  name: string;
  type?: string;
  isOptional: boolean;
  defaultValue?: string;
}

interface Property {
  name: string;
  visibility: 'public' | 'protected' | 'private';
  isStatic: boolean;
  type?: string;
  defaultValue?: string;
}

/**
 * Parse a PHP file and return its structure
 */
export function parsePhpFile(filePath: string): ParsedClass[] {
  const content = readFileSync(filePath, 'utf-8');
  return parsePhpContent(content);
}

/**
 * Parse PHP content string and return structure
 */
export function parsePhpContent(content: string): ParsedClass[] {
  const classes: ParsedClass[] = [];
  const lines = content.split('\n');
  
  // Extract namespace
  const namespaceMatch = content.match(/namespace\s+([\w\\]+)\s*;/);
  const namespace = namespaceMatch ? namespaceMatch[1] : '';
  
  // Extract use statements
  const useStatements = [...content.matchAll(/use\s+([\w\\]+)\s*;/g)].map(m => m[1]);
  
  // Extract classes
  const classMatches = [...content.matchAll(/class\s+(\w+)(?:\s+extends\s+(\w+))?(?:\s+implements\s+([\w\s,]+))?/g)];
  
  for (const classMatch of classMatches) {
    const className = classMatch[1];
    const parsedClass: ParsedClass = {
      name: className,
      namespace,
      extends: classMatch[2],
      implements: classMatch[3] ? classMatch[3].split(',').map(s => s.trim()) : [],
      properties: extractProperties(content, className),
      methods: extractMethods(content, lines),
      useStatements,
    };
    classes.push(parsedClass);
  }
  
  return classes;
}

/**
 * Extract methods from PHP content
 */
function extractMethods(content: string, lines: string[]): Method[] {
  const methods: Method[] = [];
  const methodRegex = /(?:(public|protected|private)\s+)?(?:(static)\s+)?function\s+(\w+)\s*\(([^)]*)\)(?:\s*:\s*(\??[\w\\|]+))?/g;
  
  let match;
  while ((match = methodRegex.exec(content)) !== null) {
    const methodName = match[3];
    const params = parseParameters(match[4]);
    
    methods.push({
      name: methodName,
      visibility: match[1] as Method['visibility'] || 'public',
      isStatic: !!match[2],
      parameters: params,
      returnType: match[5],
      body: extractMethodBody(content, match.index),
      lineStart: getLineNumber(content, match.index),
      lineEnd: 0, // Would need brace matching
    });
  }
  
  return methods;
}

/**
 * Parse method parameters
 */
function parseParameters(paramString: string): Parameter[] {
  if (!paramString.trim()) return [];
  
  return paramString.split(',').map(p => {
    const trimmed = p.trim();
    const parts = trimmed.split('=');
    const nameWithType = parts[0].trim();
    const nameParts = nameWithType.split(/\s+/);
    
    return {
      name: nameParts[nameParts.length - 1].replace('$', ''),
      type: nameParts.length > 1 ? nameParts.slice(0, -1).join(' ') : undefined,
      isOptional: parts.length > 1,
      defaultValue: parts[1]?.trim(),
    };
  });
}

/**
 * Extract method body (simplified - gets content until next method or class end)
 */
function extractMethodBody(content: string, startIndex: number): string {
  // Find the opening brace
  const braceIndex = content.indexOf('{', startIndex);
  if (braceIndex === -1) return '';
  
  // Count braces to find matching close
  let depth = 1;
  let i = braceIndex + 1;
  while (depth > 0 && i < content.length) {
    if (content[i] === '{') depth++;
    if (content[i] === '}') depth--;
    i++;
  }
  
  return content.substring(braceIndex + 1, i - 1).trim();
}

/**
 * Get line number from character index
 */
function getLineNumber(content: string, index: number): number {
  return content.substring(0, index).split('\n').length;
}

/**
 * Extract properties from a class
 */
function extractProperties(content: string, className: string): Property[] {
  const properties: Property[] = [];
  const propRegex = /(?:(public|protected|private)\s+)?(?:(static)\s+)?\$(\w+)(?:\s*=\s*([^;]+))?;/g;
  
  // Find class body first
  const classStart = content.indexOf(`class ${className}`);
  if (classStart === -1) return properties;
  
  const classBody = extractMethodBody(content, classStart);
  
  let match;
  while ((match = propRegex.exec(classBody)) !== null) {
    properties.push({
      name: match[3],
      visibility: match[1] as Property['visibility'] || 'protected',
      isStatic: !!match[2],
      defaultValue: match[4]?.trim(),
    });
  }
  
  return properties;
}
