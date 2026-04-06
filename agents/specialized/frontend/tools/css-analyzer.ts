/**
 * CSS Analyzer Utility
 * 
 * Analyze CSS usage in Blade templates:
 * - Find duplicated styles
 * - Identify unused CSS classes
 * - Check consistency with Admins template
 * - Suggest Bootstrap utility classes
 */

import { listFilesRecursive, readFile, searchInFiles } from '../../../shared/file-operations';

interface CssAnalysis {
  duplicatedStyles: DuplicatedStyle[];
  unusedClasses: string[];
  inconsistencies: Inconsistency[];
  suggestions: CssSuggestion[];
}

interface DuplicatedStyle {
  style: string;
  files: string[];
  suggestion: string;
}

interface Inconsistency {
  property: string;
  variations: string[];
  recommended: string;
}

interface CssSuggestion {
  file: string;
  line: number;
  current: string;
  suggested: string;
  reason: string;
}

/**
 * Analyze CSS across all Blade templates
 */
export function analyzeCss(basePath: string): CssAnalysis {
  const bladeFiles = listFilesRecursive(`${basePath}/resources/views`, '.blade.php');
  const result: CssAnalysis = {
    duplicatedStyles: [],
    unusedClasses: [],
    inconsistencies: [],
    suggestions: [],
  };

  // Check for inline styles that should be classes
  for (const file of bladeFiles) {
    const content = readFile(file);
    const inlineStyleMatches = content.matchAll(/style="([^"]+)"/g);
    
    for (const match of inlineStyleMatches) {
      result.suggestions.push({
        file,
        line: 0,
        current: match[1],
        suggested: 'Extract to CSS class or use Bootstrap utility',
        reason: 'Inline styles are harder to maintain',
      });
    }
  }

  // Check for Bootstrap utility class opportunities
  const bootstrapMap: Record<string, string> = {
    'margin-left: 0': 'ms-0',
    'margin-right: 0': 'me-0',
    'margin-top: 0': 'mt-0',
    'margin-bottom: 0': 'mb-0',
    'padding: 0': 'p-0',
    'text-align: center': 'text-center',
    'text-align: right': 'text-end',
    'text-align: left': 'text-start',
    'font-weight: bold': 'fw-bold',
    'display: none': 'd-none',
    'display: block': 'd-block',
    'display: flex': 'd-flex',
    'width: 100%': 'w-100',
    'color: red': 'text-danger',
    'color: green': 'text-success',
  };

  for (const [style, bootstrapClass] of Object.entries(bootstrapMap)) {
    const files = searchInFiles(`${basePath}/resources/views`, new RegExp(style.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')), '.blade.php');
    
    for (const file of files) {
      result.suggestions.push({
        file: file.file,
        line: file.line,
        current: style,
        suggested: bootstrapClass,
        reason: 'Use Bootstrap utility class instead',
      });
    }
  }

  return result;
}

/**
 * Check if a class exists in Admins template CSS
 */
export function validateClassExists(className: string, basePath: string): boolean {
  const cssFiles = listFilesRecursive(`${basePath}/public/admins/css`, '.css');
  
  for (const cssFile of cssFiles) {
    const content = readFile(cssFile);
    if (content.includes(`.${className}`)) {
      return true;
    }
  }
  
  return false;
}
