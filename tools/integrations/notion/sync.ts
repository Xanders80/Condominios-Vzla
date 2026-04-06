/**
 * Notion Integration
 * 
 * Sync project documentation with Notion:
 * - Update PRDs
 * - Log architecture decisions
 * - Track meeting notes
 */

interface NotionConfig {
  apiKey: string;
  databaseId: string;
  baseUrl: string;
}

interface NotionPage {
  title: string;
  properties: Record<string, string>;
  content: NotionBlock[];
}

interface NotionBlock {
  type: string;
  text?: { content: string };
  rich_text?: { text: { content: string } }[];
}

/**
 * Create a page in Notion database
 */
export async function createNotionPage(config: NotionConfig, page: NotionPage): Promise<string | null> {
  const url = `${config.baseUrl}/pages`;
  
  const body = {
    parent: { database_id: config.databaseId },
    properties: {
      Name: {
        title: [{ text: { content: page.title } }],
      },
      ...Object.entries(page.properties).reduce((acc, [key, value]) => {
        acc[key] = { rich_text: [{ text: { content: value } }] };
        return acc;
      }, {} as Record<string, unknown>),
    },
    children: page.content,
  };
  
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${config.apiKey}`,
        'Notion-Version': '2022-06-28',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(body),
    });
    
    if (!response.ok) return null;
    const data = await response.json();
    return data.url;
  } catch {
    return null;
  }
}

/**
 * Update an existing Notion page
 */
export async function updateNotionPage(config: NotionConfig, pageId: string, properties: Record<string, string>): Promise<boolean> {
  const url = `${config.baseUrl}/pages/${pageId}`;
  
  const body = {
    properties: Object.entries(properties).reduce((acc, [key, value]) => {
      acc[key] = { rich_text: [{ text: { content: value } }] };
      return acc;
    }, {} as Record<string, unknown>),
  };
  
  try {
    const response = await fetch(url, {
      method: 'PATCH',
      headers: {
        'Authorization': `Bearer ${config.apiKey}`,
        'Notion-Version': '2022-06-28',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(body),
    });
    
    return response.ok;
  } catch {
    return false;
  }
}

/**
 * Log an architecture decision to Notion
 */
export async function logADR(config: NotionConfig, adr: {
  number: string;
  title: string;
  status: string;
  decision: string;
  consequences: string;
}): Promise<string | null> {
  return createNotionPage(config, {
    title: `ADR-${adr.number}: ${adr.title}`,
    properties: {
      Status: adr.status,
      Type: 'Architecture Decision',
    },
    content: [
      { type: 'heading_2', rich_text: [{ text: { content: 'Decision' } }] },
      { type: 'paragraph', rich_text: [{ text: { content: adr.decision } }] },
      { type: 'heading_2', rich_text: [{ text: { content: 'Consequences' } }] },
      { type: 'paragraph', rich_text: [{ text: { content: adr.consequences } }] },
    ],
  });
}
