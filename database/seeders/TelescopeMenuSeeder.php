<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class TelescopeMenuSeeder extends Seeder
{
    public function run()
    {
        // Crear menú de Telescope en el footer
        Menu::updateOrCreate([
            'title' => 'Telescope',
            'url' => 'telescope',
            'icon' => 'fa fa-bug',
            'sort' => 100,
            'parent_id' => null,
        ]);
    }
}
