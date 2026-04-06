/**
 * GitHub Issue Manager
 * 
 * Manage issues for:
 * - Bug reports from error-handler killer
 * - Feature requests from product-manager
 * - Technical debt from tech-lead
 */

import { createIssue, addComment, GitHubConfig, IssueResult } from './pr-creator';

interface BugReport {
  title: string;
  description: string;
  steps: string[];
  expected: string;
  actual: string;
  severity: 'critical' | 'high' | 'medium' | 'low';
  module?: string;
}

interface FeatureRequest {
  title: string;
  description: string;
  userStory: string;
  acceptanceCriteria: string[];
  priority: 'must' | 'should' | 'could' | 'wont';
}

/**
 * Create a bug report issue
 */
export async function createBugReport(config: GitHubConfig, bug: BugReport): Promise<IssueResult> {
  const body = formatBugReport(bug);
  
  return createIssue(config, {
    title: `🐛 ${bug.title}`,
    body,
    labels: ['bug', bug.severity, bug.module || 'general'].filter(Boolean),
  });
}

/**
 * Create a feature request issue
 */
export async function createFeatureRequest(config: GitHubConfig, feature: FeatureRequest): Promise<IssueResult> {
  const body = formatFeatureRequest(feature);
  
  return createIssue(config, {
    title: `✨ ${feature.title}`,
    body,
    labels: ['enhancement', feature.priority].filter(Boolean),
  });
}

/**
 * Add analysis to an existing issue
 */
export async function addAnalysis(config: GitHubConfig, issueNumber: number, analysis: string): Promise<void> {
  await addComment(config, issueNumber, `## 🔍 Analysis\n\n${analysis}`);
}

/**
 * Add fix proposal to an existing issue
 */
export async function addFixProposal(config: GitHubConfig, issueNumber: number, proposal: string): Promise<void> {
  await addComment(config, issueNumber, `## 💡 Fix Proposal\n\n${proposal}`);
}

function formatBugReport(bug: BugReport): string {
  return `## Description
${bug.description}

## Steps to Reproduce
${bug.steps.map((s, i) => `${i + 1}. ${s}`).join('\n')}

## Expected Behavior
${bug.expected}

## Actual Behavior
${bug.actual}

## Severity
${bug.severity.toUpperCase()}

## Module
${bug.module || 'General'}

---
*Reported by OpenCode Error Handler*`;
}

function formatFeatureRequest(feature: FeatureRequest): string {
  return `## User Story
${feature.userStory}

## Description
${feature.description}

## Acceptance Criteria
${feature.acceptanceCriteria.map((c, i) => `- [ ] ${c}`).join('\n')}

## Priority
${feature.priority.toUpperCase()}

---
*Created by OpenCode Product Manager*`;
}
