/**
 * Comment Extractor Utility
 * 
 * Extract comments and docblocks from PHP files:
 * - PHPDoc blocks
 * - Single-line comments
 * - Multi-line comments
 * - TODO/FIXME/HACK markers
 */

import { readFile, searchInFiles } from '../file-operations';

interface DocBlock {
  file: string;
  line: number;
  target: string; // class, method, or property name
  summary: string;
  description: string;
  tags: DocTag[];
}

interface DocTag {
  name: string;
  value: string;
}

interface TodoComment {
  file: string;
  line: number;
  type: 'TODO' | 'FIXME' | 'HACK' | 'XXX' | 'NOTE';
  content: string;
}

/**
 * Extract all docblocks from a PHP file
 */
export function extractDocBlocks(filePath: string): DocBlock[] {
  const content = readFile(filePath);
  const docBlocks: DocBlock[] = [];
  
  // Match docblocks followed by class, method, or property
  const docblockRegex = /\/\*\*([\s\S]*?)\*\/\s*(?:(?:public|protected|private|static|abstract|final)\s+)?(?:function\s+(\w+)|class\s+(\w+)|\$\w+)/g;
  let match;
  
  while ((match = docblockRegex.exec(content)) !== null) {
    const docContent = match[1];
    const target = match[2] || match[3] || 'unknown';
    const lines = docContent.split('\n');
    
    const tags: DocTag[] = [];
    const descriptionLines: string[] = [];
    let summary = '';
    
    for (const line of lines) {
      const trimmed = line.replace(/^\s*\*\s?/, '').trim();
      if (trimmed.startsWith('@')) {
        const tagMatch = trimmed.match(/^@(\w+)\s*(.*)/);
        if (tagMatch) {
          tags.push({ name: tagMatch[1], value: tagMatch[2] });
        }
      } else if (!summary && trimmed) {
        summary = trimmed;
      } else if (trimmed) {
        descriptionLines.push(trimmed);
      }
    }
    
    docBlocks.push({
      file: filePath,
      line: content.substring(0, match.index).split('\n').length,
      target,
      summary,
      description: descriptionLines.join('\n'),
      tags,
    });
  }
  
  return docBlocks;
}

/**
 * Find all TODO/FIXME comments in the codebase
 */
export function findTodos(basePath: string): TodoComment[] {
  const results = searchInFiles(basePath, /\/\/\s*(TODO|FIXME|HACK|XXX|NOTE)\s*:?\s*(.*)/i, '.php');
  
  return results.map(r => {
    const match = r.content.match(/\/\/\s*(TODO|FIXME|HACK|XXX|NOTE)\s*:?\s*(.*)/i);
    return {
      file: r.file,
      line: r.line,
      type: (match?.[1] || 'TODO').toUpperCase() as TodoComment['type'],
      content: match?.[2] || r.content,
    };
  });
}

/**
 * Check if a method has a docblock
 */
export function hasDocBlock(content: string, methodName: string): boolean {
  const methodIndex = content.indexOf(`function ${methodName}`);
  if (methodIndex === -1) return false;
  
  // Look backwards for */
  const before = content.substring(0, methodIndex).trimEnd();
  return before.endsWith('*/');
}

/**
 * Get missing docblocks report
 */
export function getMissingDocBlocks(filePath: string): string[] {
  const content = readFile(filePath);
  const missing: string[] = [];
  
  // Find all public/protected methods
  const methodRegex = /(?:public|protected)\s+(?:static\s+)?function\s+(\w+)/g;
  let match;
  
  while ((match = methodRegex.exec(content)) !== null) {
    const methodName = match[1];
    if (!hasDocBlock(content, methodName)) {
      missing.push(methodName);
    }
  }
  
  return missing;
}
