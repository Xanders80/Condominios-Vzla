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
        $this->model = $menu->model ?? 'dashboard';
        $this->url = $menu->url ?? 'dashboard';
        $this->view = config('master.app.view.backend') . '.' . $this->code;
    }
}
