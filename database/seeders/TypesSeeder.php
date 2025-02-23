<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TypesSeeder extends Seeder
{
    public function run()
    {
        $groups = json_decode(File::get(database_path('seeders/backup/document-types.json')), true);
        foreach ($groups as $item) {
            \App\Models\DocumentType::updateOrCreate(['name' => trans($item['name'])]);
        }

        $groups = json_decode(File::get(database_path('seeders/backup/dweller-types.json')), true);
        foreach ($groups as $item) {
            \App\Models\DwellerType::updateOrCreate(['name' => trans($item['name'])]);
        }

        $groups = json_decode(File::get(database_path('seeders/backup/unit-types.json')), true);
        foreach ($groups as $item) {
            \App\Models\UnitType::updateOrCreate(['name' => trans($item['name'])]);
        }

        $groups = json_decode(File::get(database_path('seeders/backup/banks.json')), true);
        foreach ($groups as $item) {
            \App\Models\Banks::updateOrCreate([
                'code_sudebank' => $item['code_sudebank'],
                'name_ibp' => $item['name_ibp'],
                'rif' => $item['rif'],
                'website' => $item['website'],
                'active' => $item['active'],
            ]);
        }

        $groups = json_decode(File::get(database_path('seeders/backup/ways-to-pays.json')), true);
        foreach ($groups as $item) {
            \App\Models\WaysToPays::updateOrCreate(['name' => trans($item['name'])]);
        }
    }
}
