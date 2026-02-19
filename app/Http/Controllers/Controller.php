<?php

namespace App\Http\Controllers;

use App\Support\Helper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use ApiResponseTrait;

    public string $view;
    public string $code;
    public string $model;
    public string $url;
    public object $help;

    public function __construct(Helper $helper)
    {
        $this->help = $helper;
        $menu = $helper->menu();
        $this->code = $menu->code ?? 'dashboard';
        $menuModel = $menu->model ?? 'dashboard';
        // Resolver nombre de modelo a la clase en App\Models si es posible
        if (is_string($menuModel) && !str_contains($menuModel, '\\')) {
            $studly = \Illuminate\Support\Str::studly($menuModel);
            $candidate = "App\\Models\\{$studly}";
            if (class_exists($candidate)) {
                $this->model = $candidate;
            } else {
                // Fallback seguro a User para evitar errores de cadena como 'dashboard'
                $this->model = \App\Models\User::class;
            }
        } else {
            $this->model = is_string($menuModel) ? $menuModel : \App\Models\User::class;
        }
        $this->url = $menu->url ?? 'dashboard';
        $this->view = config('master.app.view.backend') . '.' . $this->code;
    }
}
