<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewModulesMenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Gestión Financiera (Parent)
        $finance = Menu::updateOrCreate(
            ['code' => 'finance'],
            [
                'title' => __('Financial Management'),
                'subtitle' => __('Invoices, Interests and Delinquency'),
                'model' => 'Models',
                'url' => 'finance',
                'icon' => 'fa fa-money',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 10,
            ]
        );

        $this->createChildren($finance->id, [
            [
                'title' => __('Receipts'),
                'subtitle' => __('Management of receipts'),
                'code' => 'receipts',
                'model' => 'Receipt',
                'url' => 'receipts',
                'icon' => 'fa fa-file-text-o',
                'sort' => 1,
            ],
            [
                'title' => __('Interest Rates'),
                'subtitle' => __('Historical interest rates'),
                'code' => 'interest-rates',
                'model' => 'InterestRate',
                'url' => 'interest-rates',
                'icon' => 'fa fa-percent',
                'sort' => 2,
            ],
            [
                'title' => __('Delinquency'),
                'subtitle' => __('Debt management'),
                'code' => 'debts',
                'model' => 'Debt',
                'url' => 'debts',
                'icon' => 'fa fa-warning',
                'sort' => 3,
            ],
        ]);

        // 2. Servicios (Parent)
        $services = Menu::updateOrCreate(
            ['code' => 'services'],
            [
                'title' => __('Services & Areas'),
                'subtitle' => __('Common areas and maintenance'),
                'model' => 'Models',
                'url' => 'services',
                'icon' => 'fa fa-wrench',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 11,
            ]
        );

        $this->createChildren($services->id, [
            [
                'title' => __('Common Areas'),
                'subtitle' => __('Manage social areas'),
                'code' => 'common-areas',
                'model' => 'CommonArea',
                'url' => 'common-areas',
                'icon' => 'fa fa-tree',
                'sort' => 1,
            ],
            [
                'title' => __('Bookings'),
                'subtitle' => __('Area reservations'),
                'code' => 'bookings',
                'model' => 'CommonAreaBooking',
                'url' => 'bookings',
                'icon' => 'fa fa-calendar-check-o',
                'sort' => 2,
            ],
            [
                'title' => __('Suppliers'),
                'subtitle' => __('Manage suppliers'),
                'code' => 'suppliers',
                'model' => 'Supplier',
                'url' => 'suppliers',
                'icon' => 'fa fa-truck',
                'sort' => 3,
            ],
            [
                'title' => __('Incident Reports'),
                'subtitle' => __('Reported problems'),
                'code' => 'incident-reports',
                'model' => 'IncidentReport',
                'url' => 'incident-reports',
                'icon' => 'fa fa-exclamation-triangle',
                'sort' => 4,
            ],
            [
                'title' => __('Work Orders'),
                'subtitle' => __('Maintenance tasks'),
                'code' => 'work-orders',
                'model' => 'WorkOrder',
                'url' => 'work-orders',
                'icon' => 'fa fa-tasks',
                'sort' => 5,
            ],
        ]);

        // 3. Gobierno (Parent)
        $governance = Menu::updateOrCreate(
            ['code' => 'governance'],
            [
                'title' => __('Community & Governance'),
                'subtitle' => __('Assemblies and voting'),
                'model' => 'Models',
                'url' => 'governance',
                'icon' => 'fa fa-gavel',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 12,
            ]
        );

        $this->createChildren($governance->id, [
            [
                'title' => __('Assemblies'),
                'subtitle' => __('Session management'),
                'code' => 'assembly-sessions',
                'model' => 'AssemblySession',
                'url' => 'assembly-sessions',
                'icon' => 'fa fa-users',
                'sort' => 1,
            ],
            [
                'title' => __('Motions'),
                'subtitle' => __('Proposals and voting'),
                'code' => 'motions',
                'model' => 'Motion',
                'url' => 'motions',
                'icon' => 'fa fa-archive',
                'sort' => 2,
            ],
        ]);
    }

    private function createChildren($parentId, $children)
    {
        foreach ($children as $child) {
            Menu::updateOrCreate(
                ['code' => $child['code']],
                array_merge($child, [
                    'parent_id' => $parentId,
                    'type' => 'backend',
                    'show' => true,
                    'active' => true,
                ])
            );
        }
    }
}
