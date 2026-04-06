# Model Template

## Input Variables
- `{{model_name}}` - Model class name (e.g., "Supplier")
- `{{table_name}}` - Database table name (e.g., "suppliers")
- `{{fillable}}` - Fillable fields (comma-separated)
- `{{casts}}` - Casts definitions (key: type pairs)
- `{{relationships}}` - Relationship method definitions
- `{{soft_deletes}}` - Whether model uses soft deletes (true/false)
- `{{scopes}}` - Query scope definitions

## Template

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;{{#if soft_deletes}}
use Illuminate\Database\Eloquent\SoftDeletes;{{/if}}
{{#if has_belongs_to}}
use Illuminate\Database\Eloquent\Relations\BelongsTo;{{/if}}
{{#if has_has_many}}
use Illuminate\Database\Eloquent\Relations\HasMany;{{/if}}

class {{model_name}} extends Model
{
{{#if soft_deletes}}
    use SoftDeletes;

{{/if}}
    protected $table = '{{table_name}}';

    protected $fillable = [
{{#each fillable}}
        '{{this}}',
{{/each}}
    ];

    protected $casts = [
{{#each casts}}
        '{{@key}}' => '{{this}}',
{{/each}}
    ];

{{#each relationships}}
    /**
     * {{description}}
     */
    public function {{name}}(): {{relation_type}}
    {
        return $this->{{method}}({{related_model}}::class{{#if foreign_key}}, '{{foreign_key}}'{{/if}});
    }

{{/each}}
{{#each scopes}}
    /**
     * {{description}}
     */
    public function scope{{name}}($query{{#if params}}, {{params}}{{/if}})
    {
        return $query->{{condition}};
    }

{{/each}}
}
```
