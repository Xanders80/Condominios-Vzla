# Few-Shot Example: Database Migration

## Input
Create a migration for "suppliers" table with address fields and relationships.

## Output

### `database/migrations/2026_01_17_100001_create_suppliers_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rif')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('country_addresses')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('state_addresses')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('city_addresses')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
```
