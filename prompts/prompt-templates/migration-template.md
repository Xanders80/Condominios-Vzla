# Migration Template

## Input Variables
- `{{table_name}}` - Table name (e.g., "suppliers")
- `{{timestamp}}` - Migration timestamp prefix
- `{{columns}}` - Column definitions
- `{{foreign_keys}}` - Foreign key definitions
- `{{indexes}}` - Index definitions
- `{{soft_deletes}}` - Whether to add soft deletes
- `{{timestamps}}` - Whether to add timestamps

## Template

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{{table_name}}', function (Blueprint $table) {
            $table->id();
{{#each columns}}
            $table->{{type}}('{{name}}'){{#if nullable}}->nullable(){{/if}}{{#if default}}->default({{default}}){{/if}}{{#if unique}}->unique(){{/if}}{{#if comment}}->comment('{{comment}}'){{/if}};
{{/each}}
{{#if soft_deletes}}
            $table->softDeletes();
{{/if}}
{{#if timestamps}}
            $table->timestamps();
{{/if}}

{{#each foreign_keys}}
            $table->foreignId('{{column}}')->{{#if nullable}}nullable()->{{/if}}constrained('{{table}}')->{{#if cascade}}cascadeOnDelete(){{else}}nullOnDelete(){{/if}};
{{/each}}

{{#each indexes}}
            $table->index([{{columns}}]{{#if name}}, '{{name}}'{{/if}});
{{/each}}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{{table_name}}');
    }
};
```
