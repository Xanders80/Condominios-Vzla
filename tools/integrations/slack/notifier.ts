/**
 * Slack Integration
 * 
 * Send notifications to Slack channels:
 * - Deployment notifications
 * - Error alerts from killers
 * - Test result summaries
 */

interface SlackConfig {
  webhookUrl: string;
  channel?: string;
  username?: string;
  iconEmoji?: string;
}

interface SlackMessage {
  text: string;
  blocks?: SlackBlock[];
  attachments?: SlackAttachment[];
}

interface SlackBlock {
  type: string;
  text?: { type: string; text: string };
  fields?: { type: string; text: string }[];
}

interface SlackAttachment {
  color: string;
  title: string;
  text: string;
  fields?: { title: string; value: string; short: boolean }[];
}

/**
 * Send a message to Slack
 */
export async function sendSlackMessage(config: SlackConfig, message: SlackMessage): Promise<boolean> {
  const payload: Record<string, unknown> = {
    text: message.text,
  };
  
  if (config.channel) payload.channel = config.channel;
  if (config.username) payload.username = config.username;
  if (config.iconEmoji) payload.icon_emoji = config.iconEmoji;
  if (message.blocks) payload.blocks = message.blocks;
  if (message.attachments) payload.attachments = message.attachments;
  
  try {
    const response = await fetch(config.webhookUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    
    return response.ok;
  } catch {
    return false;
  }
}

/**
 * Send deployment notification
 */
export async function notifyDeployment(config: SlackConfig, details: {
  environment: string;
  version: string;
  branch: string;
  deployer: string;
  duration: string;
}): Promise<boolean> {
  return sendSlackMessage(config, {
    text: `:rocket: Deployed to *${details.environment}*`,
    attachments: [{
      color: details.environment === 'production' ? '#ff0000' : '#36a64f',
      title: `Deployment - ${details.version}`,
      text: `Branch: ${details.branch}\nDeployer: ${details.deployer}\nDuration: ${details.duration}`,
      fields: [
        { title: 'Environment', value: details.environment, short: true },
        { title: 'Branch', value: details.branch, short: true },
      ],
    }],
  });
}

/**
 * Send test result summary
 */
export async function notifyTestResults(config: SlackConfig, results: {
  passed: number;
  failed: number;
  skipped: number;
  duration: string;
}): Promise<boolean> {
  const color = results.failed === 0 ? '#36a64f' : '#ff0000';
  const emoji = results.failed === 0 ? ':white_check_mark:' : ':x:';
  
  return sendSlackMessage(config, {
    text: `${emoji} Tests: ${results.passed} passed, ${results.failed} failed, ${results.skipped} skipped (${results.duration})`,
    attachments: [{
      color,
      title: 'Test Results',
      text: `Passed: ${results.passed}\nFailed: ${results.failed}\nSkipped: ${results.skipped}`,
    }],
  });
}

/**
 * Send error alert
 */
export async function notifyError(config: SlackConfig, error: {
  type: string;
  message: string;
  file?: string;
  line?: number;
}): Promise<boolean> {
  return sendSlackMessage(config, {
    text: `:rotating_light: *${error.type}*`,
    attachments: [{
      color: '#ff0000',
      title: error.type,
      text: `${error.message}${error.file ? `\nFile: ${error.file}:${error.line}` : ''}`,
    }],
  });
}
