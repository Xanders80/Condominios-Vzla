<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class VzlaSeeder extends Seeder
{
    public function run()
    {
        $groups = json_decode(File::get(database_path('seeders/backup/states.json')), true);
        foreach ($groups as $item) {
            \App\Models\StateAddress::updateOrCreate([
                'id' => $item['id'],
                'name' => $item['name'],
                'iso_3166_2' => $item['iso_3166_2'],
            ]);
        }

        $groups = json_decode(File::get(database_path('seeders/backup/municipalities.json')), true);
        foreach ($groups as $item) {
            \App\Models\MunicipalityAddress::updateOrCreate([
                'id' => $item['id'],
                'state_id' => $item['state_id'],
                'name' => $item['name'],
            ]);
        }

        $groups = json_decode(File::get(database_path('seeders/backup/cities.json')), true);
        foreach ($groups as $item) {
            \App\Models\CityAddress::updateOrCreate([
                'id' => $item['id'],
                'state_id' => $item['state_id'],
                'municipality_id' => $item['municipality_id'],
                'name' => $item['name'],
            ]);
        }

        $groups = json_decode(File::get(database_path('seeders/backup/parishes.json')), true);
        foreach ($groups as $item) {
            \App\Models\CountryAddress::updateOrCreate([
                'id' => $item['id'],
                'municipality_id' => $item['municipality_id'],
                'name' => $item['name'],
            ]);
        }

        $groups = json_decode(File::get(database_path('seeders/backup/postal-zone.json')), true);
        foreach ($groups as $item) {
            \App\Models\PostalCodeAddress::updateOrCreate([
                'id' => $item['id'],
                'parish_id' => $item['parish_id'],
                'name' => $item['name'],
                'zip_code' => $item['zip_code'],
            ]);
        }
    }
}
