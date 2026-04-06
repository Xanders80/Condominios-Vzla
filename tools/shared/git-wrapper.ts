/**
 * Git Wrapper Utility
 * 
 * Execute Git commands and parse results:
 * - Get current branch
 * - Get modified files
 * - Get commit history
 * - Stage and commit changes
 */

import { execSync } from 'child_process';

/**
 * Execute a git command and return output
 */
function git(args: string, cwd?: string): string {
  return execSync(`git ${args}`, { cwd, encoding: 'utf-8' }).trim();
}

/**
 * Get the current branch name
 */
export function getCurrentBranch(cwd?: string): string {
  return git('branch --show-current', cwd);
}

/**
 * Get list of modified files (staged and unstaged)
 */
export function getModifiedFiles(cwd?: string): ModifiedFiles {
  const staged = git('diff --cached --name-only', cwd).split('\n').filter(Boolean);
  const unstaged = git('diff --name-only', cwd).split('\n').filter(Boolean);
  const untracked = git('ls-files --others --exclude-standard', cwd).split('\n').filter(Boolean);
  
  return { staged, unstaged, untracked };
}

/**
 * Get recent commit history
 */
export function getCommitHistory(count: number = 10, cwd?: string): Commit[] {
  const format = '%H|%an|%ae|%ai|%s';
  const output = git(`log -${count} --format="${format}"`, cwd);
  
  return output.split('\n').filter(Boolean).map(line => {
    const [hash, authorName, authorEmail, date, subject] = line.split('|');
    return { hash, authorName, authorEmail, date: new Date(date), subject };
  });
}

/**
 * Get diff for a specific file or all changes
 */
export function getDiff(file?: string, cached: boolean = false, cwd?: string): string {
  const flag = cached ? '--cached' : '';
  const fileArg = file ? ` -- ${file}` : '';
  return git(`diff ${flag}${fileArg}`, cwd);
}

/**
 * Stage files
 */
export function stageFiles(files: string | string[], cwd?: string): void {
  const fileList = Array.isArray(files) ? files.join(' ') : files;
  git(`add ${fileList}`, cwd);
}

/**
 * Stage all changes
 */
export function stageAll(cwd?: string): void {
  git('add -A', cwd);
}

/**
 * Create a commit
 */
export function commit(message: string, cwd?: string): string {
  return git(`commit -m "${message}"`, cwd);
}

/**
 * Check if working tree is clean
 */
export function isClean(cwd?: string): boolean {
  try {
    git('diff --quiet', cwd);
    git('diff --cached --quiet', cwd);
    return true;
  } catch {
    return false;
  }
}

/**
 * Get remote URL
 */
export function getRemoteUrl(remote: string = 'origin', cwd?: string): string {
  return git(`remote get-url ${remote}`, cwd);
}

/**
 * Check if a branch exists
 */
export function branchExists(branchName: string, remote: boolean = false, cwd?: string): boolean {
  try {
    if (remote) {
      git(`ls-remote --heads origin ${branchName}`, cwd);
    } else {
      git(`rev-parse --verify ${branchName}`, cwd);
    }
    return true;
  } catch {
    return false;
  }
}

export interface ModifiedFiles {
  staged: string[];
  unstaged: string[];
  untracked: string[];
}

export interface Commit {
  hash: string;
  authorName: string;
  authorEmail: string;
  date: Date;
  subject: string;
}
