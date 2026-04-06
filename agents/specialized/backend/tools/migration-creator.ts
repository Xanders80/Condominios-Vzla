/**
 * Migration Creator Utility
 * 
 * Generate Laravel migration files:
 * - Create table migrations
 * - Add column migrations
 * - Add foreign key migrations
 * - Drop table migrations
 */

import { writeFile, fileExists } from '../../shared/file-operations';

interface ColumnDef {
  name: string;
  type: string;
  nullable?: boolean;
  default?: string;
  unique?: boolean;
  index?: boolean;
  comment?: string;
}

interface ForeignKeyDef {
  column: string;
  table: string;
  nullable?: boolean;
  cascadeOnDelete?: boolean;
}

interface MigrationConfig {
  tableName: string;
  columns: ColumnDef[];
  foreignKeys?: ForeignKeyDef[];
  indexes?: string[][];
  softDeletes?: boolean;
  timestamps?: boolean;
}

/**
 * Generate a create table migration
 */
export function createTableMigration(basePath: string, config: MigrationConfig): string {
  const timestamp = new Date().toISOString().replace(/[-:T]/g, '').substring(0, 14);
  const fileName = `${timestamp}_create_${config.tableName}_table.php`;
  const filePath = `${basePath}/database/migrations/${fileName}`;
  
  if (fileExists(filePath)) {
    throw new Error(`Migration already exists: ${fileName}`);
  }
  
  const content = generateCreateTableMigration(config);
  writeFile(filePath, content);
  
  return filePath;
}

function generateCreateTableMigration(config: MigrationConfig): string {
  const columns = config.columns.map(col => {
    let line = `            $table->${col.type}('${col.name}')`;
    if (col.nullable) line += '->nullable()';
    if (col.default !== undefined) line += `->default(${col.default})`;
    if (col.unique) line += '->unique()';
    if (col.index) line += '->index()';
    if (col.comment) line += `->comment('${col.comment}')`;
    line += ';';
    return line;
  }).join('\n');
  
  const foreignKeys = (config.foreignKeys || []).map(fk => {
    let line = `            $table->foreignId('${fk.column}')`;
    if (fk.nullable) line += '->nullable()';
    line += `->constrained('${fk.table}')`;
    line += fk.cascadeOnDelete ? '->cascadeOnDelete()' : '->nullOnDelete()';
    line += ';';
    return line;
  }).join('\n');
  
  const indexes = (config.indexes || []).map(cols => 
    `            $table->index(['${cols.join("', '")}']);`
  ).join('\n');
  
  const extras = [
    config.softDeletes ? '            $table->softDeletes();' : '',
    config.timestamps !== false ? '            $table->timestamps();' : '',
  ].filter(Boolean).join('\n');
  
  const sections = [columns, foreignKeys, indexes, extras].filter(s => s).join('\n\n');
  
  return `<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('${config.tableName}', function (Blueprint $table) {
            $table->id();

${sections}
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('${config.tableName}');
    }
};
`;
}

/**
 * Generate an add column migration
 */
export function addColumnMigration(basePath: string, tableName: string, columns: ColumnDef[]): string {
  const timestamp = new Date().toISOString().replace(/[-:T]/g, '').substring(0, 14);
  const fileName = `${timestamp}_add_columns_to_${tableName}_table.php`;
  const filePath = `${basePath}/database/migrations/${fileName}`;
  
  const columnDefs = columns.map(col => {
    let line = `            $table->${col.type}('${col.name}')`;
    if (col.nullable) line += '->nullable()';
    if (col.default !== undefined) line += `->default(${col.default})`;
    line += ';';
    return line;
  }).join('\n');
  
  const content = `<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('${tableName}', function (Blueprint $table) {
${columnDefs}
        });
    }

    public function down(): void
    {
        Schema::table('${tableName}', function (Blueprint $table) {
${columns.map(col => `            $table->dropColumn('${col.name}');`).join('\n')}
        });
    }
};
`;
  
  writeFile(filePath, content);
  return filePath;
}
