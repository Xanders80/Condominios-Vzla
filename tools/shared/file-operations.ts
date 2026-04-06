/**
 * File Operations Utility
 * 
 * Common file operations used by agents and subagents:
 * - Read/write files
 * - Search file contents
 * - List directory contents
 * - Create directories recursively
 */

import { readFileSync, writeFileSync, existsSync, mkdirSync, readdirSync, statSync, unlinkSync, renameSync } from 'fs';
import { join, dirname, extname, basename } from 'path';

/**
 * Read a file and return its content
 */
export function readFile(filePath: string): string {
  if (!existsSync(filePath)) {
    throw new Error(`File not found: ${filePath}`);
  }
  return readFileSync(filePath, 'utf-8');
}

/**
 * Write content to a file, creating directories if needed
 */
export function writeFile(filePath: string, content: string): void {
  const dir = dirname(filePath);
  if (!existsSync(dir)) {
    mkdirSync(dir, { recursive: true });
  }
  writeFileSync(filePath, content, 'utf-8');
}

/**
 * Check if a file exists
 */
export function fileExists(filePath: string): boolean {
  return existsSync(filePath);
}

/**
 * List files in a directory (non-recursive)
 */
export function listFiles(dirPath: string, extension?: string): string[] {
  if (!existsSync(dirPath)) return [];
  
  return readdirSync(dirPath)
    .filter(file => {
      const fullPath = join(dirPath, file);
      return statSync(fullPath).isFile();
    })
    .filter(file => {
      if (!extension) return true;
      return extname(file) === extension;
    });
}

/**
 * Recursively list all files in a directory
 */
export function listFilesRecursive(dirPath: string, extension?: string): string[] {
  if (!existsSync(dirPath)) return [];
  
  const files: string[] = [];
  const entries = readdirSync(dirPath, { withFileTypes: true });
  
  for (const entry of entries) {
    const fullPath = join(dirPath, entry.name);
    if (entry.isDirectory()) {
      files.push(...listFilesRecursive(fullPath, extension));
    } else if (entry.isFile()) {
      if (!extension || extname(entry.name) === extension) {
        files.push(fullPath);
      }
    }
  }
  
  return files;
}

/**
 * Search for files matching a glob pattern
 */
export function findFiles(dirPath: string, pattern: string): string[] {
  const allFiles = listFilesRecursive(dirPath);
  const regex = globToRegex(pattern);
  return allFiles.filter(f => regex.test(f));
}

/**
 * Search file contents matching a regex pattern
 */
export function searchInFiles(dirPath: string, pattern: RegExp, extension?: string): SearchResult[] {
  const files = listFilesRecursive(dirPath, extension);
  const results: SearchResult[] = [];
  
  for (const file of files) {
    try {
      const content = readFile(file);
      const lines = content.split('\n');
      
      for (let i = 0; i < lines.length; i++) {
        if (pattern.test(lines[i])) {
          results.push({
            file,
            line: i + 1,
            content: lines[i].trim(),
          });
          pattern.lastIndex = 0; // Reset regex
        }
      }
    } catch {
      // Skip binary or unreadable files
    }
  }
  
  return results;
}

/**
 * Get file stats
 */
export function getFileStats(filePath: string): FileStats {
  const stat = statSync(filePath);
  return {
    size: stat.size,
    created: stat.birthtime,
    modified: stat.mtime,
    isFile: stat.isFile(),
    isDirectory: stat.isDirectory(),
  };
}

/**
 * Delete a file
 */
export function deleteFile(filePath: string): void {
  if (existsSync(filePath)) {
    unlinkSync(filePath);
  }
}

/**
 * Rename/move a file
 */
export function renameFile(oldPath: string, newPath: string): void {
  if (!existsSync(oldPath)) {
    throw new Error(`Source file not found: ${oldPath}`);
  }
  const dir = dirname(newPath);
  if (!existsSync(dir)) {
    mkdirSync(dir, { recursive: true });
  }
  renameSync(oldPath, newPath);
}

/**
 * Convert glob pattern to regex
 */
function globToRegex(pattern: string): RegExp {
  const regex = pattern
    .replace(/\*\*/g, '___DOUBLE_STAR___')
    .replace(/\*/g, '[^/]*')
    .replace(/___DOUBLE_STAR___/g, '.*')
    .replace(/\?/g, '.');
  return new RegExp(regex);
}

export interface SearchResult {
  file: string;
  line: number;
  content: string;
}

export interface FileStats {
  size: number;
  created: Date;
  modified: Date;
  isFile: boolean;
  isDirectory: boolean;
}
