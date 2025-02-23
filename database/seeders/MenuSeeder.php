<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $data = json_decode(File::get(database_path('seeders/backup/menu.json')), true);
        foreach ($data as $item) {
            // Aplicar localización a title y subtitle
            $item['title'] = __($item['title']);
            $item['subtitle'] = __($item['subtitle']);
            if ($menu = \App\Models\Menu::updateOrCreate(collect($item)->except('children')->toArray())) {
                if (count($item['children']) > 0) {
                    $this->menuChildren($item['children'], $menu->id);
                }
            }
        }
    }

    private function menuChildren($children, $id)
    {
        foreach ($children as $item) {
            // Aplicar localización a title y subtitle
            $item['title'] = __($item['title']);
            $item['subtitle'] = __($item['subtitle']);
            if ($menu = \App\Models\Menu::updateOrCreate(collect($item)->except('children')->toArray())) {
                $menu->update(['parent_id' => $id]);
                if (count($item['children']) > 0) {
                    $this->menuChildren($item['children'], $menu->id);
                }
            }
        }
    }
}
