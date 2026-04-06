/**
 * Docker Runner Utility
 * 
 * Manage Laravel Sail Docker containers:
 * - Start/stop containers
 * - Run artisan commands in container
 * - Run npm commands in container
 * - Check container health
 */

import { execSync } from 'child_process';

interface ContainerStatus {
  name: string;
  status: 'running' | 'stopped' | 'unhealthy' | 'restarting';
  ports: string[];
  image: string;
}

/**
 * Start Laravel Sail
 */
export function sailUp(basePath: string, detached: boolean = true): string {
  const cmd = detached
    ? './vendor/bin/sail up -d'
    : './vendor/bin/sail up';
  
  return execSync(cmd, { cwd: basePath, encoding: 'utf-8' });
}

/**
 * Stop Laravel Sail
 */
export function sailDown(basePath: string): string {
  return execSync('./vendor/bin/sail down', {
    cwd: basePath,
    encoding: 'utf-8',
  });
}

/**
 * Run artisan command in Sail container
 */
export function sailArtisan(command: string, basePath: string): string {
  return execSync(`./vendor/bin/sail artisan ${command}`, {
    cwd: basePath,
    encoding: 'utf-8',
    maxBuffer: 10 * 1024 * 1024,
  });
}

/**
 * Run npm command in Sail container
 */
export function sailNpm(command: string, basePath: string): string {
  return execSync(`./vendor/bin/sail npm ${command}`, {
    cwd: basePath,
    encoding: 'utf-8',
    maxBuffer: 10 * 1024 * 1024,
  });
}

/**
 * Run any command in Sail container
 */
export function sailExec(command: string, basePath: string): string {
  return execSync(`./vendor/bin/sail exec ${command}`, {
    cwd: basePath,
    encoding: 'utf-8',
    maxBuffer: 10 * 1024 * 1024,
  });
}

/**
 * Get container status
 */
export function getContainerStatus(basePath: string): ContainerStatus[] {
  try {
    const output = execSync('./vendor/bin/sail ps --format json', {
      cwd: basePath,
      encoding: 'utf-8',
    });
    
    const containers = JSON.parse(output);
    return containers.map((c: Record<string, unknown>) => ({
      name: c.Name as string,
      status: c.State as ContainerStatus['status'],
      ports: (c.Ports as string).split(',').map(p => p.trim()),
      image: c.Image as string,
    }));
  } catch {
    return [];
  }
}

/**
 * Check if all containers are healthy
 */
export function isHealthy(basePath: string): boolean {
  const containers = getContainerStatus(basePath);
  if (containers.length === 0) return false;
  
  return containers.every(c => c.status === 'running');
}

/**
 * View container logs
 */
export function viewLogs(service: string, lines: number = 100, basePath: string): string {
  return execSync(`./vendor/bin/sail logs ${service} --tail=${lines}`, {
    cwd: basePath,
    encoding: 'utf-8',
    maxBuffer: 10 * 1024 * 1024,
  });
}

/**
 * Run migrations in Sail
 */
export function sailMigrate(basePath: string, seed: boolean = false): string {
  const cmd = seed ? 'migrate --seed --force' : 'migrate --force';
  return sailArtisan(cmd, basePath);
}
