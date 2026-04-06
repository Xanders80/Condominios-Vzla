/**
 * GitHub Integration
 * 
 * Interact with GitHub API for:
 * - Create Pull Requests
 * - Manage Issues
 * - Handle Webhooks
 * - Review PRs
 */

interface GitHubConfig {
  token: string;
  owner: string;
  repo: string;
  baseUrl?: string;
}

interface PullRequest {
  title: string;
  body: string;
  head: string;
  base: string;
  labels?: string[];
  reviewers?: string[];
}

interface Issue {
  title: string;
  body: string;
  labels?: string[];
  assignees?: string[];
}

/**
 * Create a Pull Request
 */
export async function createPullRequest(config: GitHubConfig, pr: PullRequest): Promise<PullRequestResult> {
  const url = `${config.baseUrl || 'https://api.github.com'}/repos/${config.owner}/${config.repo}/pulls`;
  
  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${config.token}`,
      'Accept': 'application/vnd.github.v3+json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      title: pr.title,
      body: pr.body,
      head: pr.head,
      base: pr.base,
    }),
  });
  
  if (!response.ok) {
    throw new Error(`Failed to create PR: ${response.statusText}`);
  }
  
  const data = await response.json();
  
  // Add labels if specified
  if (pr.labels?.length && data.number) {
    await addLabelsToIssue(config, data.number, pr.labels);
  }
  
  // Add reviewers if specified
  if (pr.reviewers?.length && data.number) {
    await requestReviewers(config, data.number, pr.reviewers);
  }
  
  return {
    number: data.number,
    url: data.html_url,
    title: data.title,
    state: data.state,
  };
}

/**
 * Create an Issue
 */
export async function createIssue(config: GitHubConfig, issue: Issue): Promise<IssueResult> {
  const url = `${config.baseUrl || 'https://api.github.com'}/repos/${config.owner}/${config.repo}/issues`;
  
  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${config.token}`,
      'Accept': 'application/vnd.github.v3+json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      title: issue.title,
      body: issue.body,
      labels: issue.labels,
      assignees: issue.assignees,
    }),
  });
  
  if (!response.ok) {
    throw new Error(`Failed to create issue: ${response.statusText}`);
  }
  
  const data = await response.json();
  
  return {
    number: data.number,
    url: data.html_url,
    title: data.title,
    state: data.state,
  };
}

/**
 * Add labels to an issue/PR
 */
async function addLabelsToIssue(config: GitHubConfig, number: number, labels: string[]): Promise<void> {
  const url = `${config.baseUrl || 'https://api.github.com'}/repos/${config.owner}/${config.repo}/issues/${number}/labels`;
  
  await fetch(url, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${config.token}`,
      'Accept': 'application/vnd.github.v3+json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ labels }),
  });
}

/**
 * Request reviewers for a PR
 */
async function requestReviewers(config: GitHubConfig, number: number, reviewers: string[]): Promise<void> {
  const url = `${config.baseUrl || 'https://api.github.com'}/repos/${config.owner}/${config.repo}/pulls/${number}/requested_reviewers`;
  
  await fetch(url, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${config.token}`,
      'Accept': 'application/vnd.github.v3+json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ reviewers }),
  });
}

/**
 * Add a comment to an issue/PR
 */
export async function addComment(config: GitHubConfig, number: number, body: string): Promise<void> {
  const url = `${config.baseUrl || 'https://api.github.com'}/repos/${config.owner}/${config.repo}/issues/${number}/comments`;
  
  await fetch(url, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${config.token}`,
      'Accept': 'application/vnd.github.v3+json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ body }),
  });
}

/**
 * Get PR diff
 */
export async function getPRDiff(config: GitHubConfig, number: number): Promise<string> {
  const url = `${config.baseUrl || 'https://api.github.com'}/repos/${config.owner}/${config.repo}/pulls/${number}`;
  
  const response = await fetch(url, {
    headers: {
      'Authorization': `Bearer ${config.token}`,
      'Accept': 'application/vnd.github.v3.diff',
    },
  });
  
  if (!response.ok) {
    throw new Error(`Failed to get PR diff: ${response.statusText}`);
  }
  
  return response.text();
}

/**
 * Handle webhook events
 */
export function handleWebhookEvent(payload: WebhookPayload): WebhookAction {
  switch (payload.action) {
    case 'opened':
    case 'reopened':
      return { type: 'pr_opened', pr: payload.pull_request };
    case 'closed':
      return payload.pull_request?.merged
        ? { type: 'pr_merged', pr: payload.pull_request }
        : { type: 'pr_closed', pr: payload.pull_request };
    case 'synchronize':
      return { type: 'pr_updated', pr: payload.pull_request };
    default:
      return { type: 'unknown', payload };
  }
}

export interface PullRequestResult {
  number: number;
  url: string;
  title: string;
  state: string;
}

export interface IssueResult {
  number: number;
  url: string;
  title: string;
  state: string;
}

export interface WebhookPayload {
  action: string;
  pull_request?: Record<string, unknown>;
  issue?: Record<string, unknown>;
}

export interface WebhookAction {
  type: string;
  pr?: Record<string, unknown>;
  payload?: Record<string, unknown>;
}
