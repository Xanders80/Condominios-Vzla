<?php

namespace App\Http\Controllers\Backend\CommonArea;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\CommonArea\StoreCommonAreaRequest;
use App\Http\Requests\Backend\CommonArea\UpdateCommonAreaRequest;
use App\Models\CommonArea;
use App\Models\Condominiums;
use App\Services\Backend\CommonAreaService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommonAreaController extends Controller
{
    protected $areaService;

    public function __construct(CommonAreaService $areaService, Helper $helper)
    {
        parent::__construct($helper);
        $this->areaService = $areaService;
        $this->model = CommonArea::class;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }
    public function store(StoreCommonAreaRequest $request): JsonResponse
    {
        $response = $this->areaService->createArea($request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }
    public function update(UpdateCommonAreaRequest $request, $id): JsonResponse
    {
        $response = $this->areaService->updateArea($id, $request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }
    public function destroy($id): JsonResponse
    {
        $response = $this->areaService->deleteArea($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function data(Request $request): JsonResponse
    {
        $data = $this->model::latest();
        return datatables()->of($data)
            ->editColumn('is_active', function ($d) {
                return $d->is_active
                    ? '<span class="badge badge-success">' . __('Active') . '</span>'
                    : '<span class="badge badge-danger">' . __('Inactive') . '</span>';
            })
            ->editColumn('booking_fee', fn($d) => $d->currency . ' ' . number_format($d->booking_fee, 2))
            ->editColumn('pricing_type', fn($d) => ucfirst($d->pricing_type))
            ->addColumn('action', fn($d) => $this->help::generateActionButtons($d->id, $request->user(), $this->url, ['edit', 'delete']))
            ->addIndexColumn()
            ->rawColumns(['action', 'is_active'])
            ->make();
    }

    private function handleViewAction(string $action, ?string $id = null): View|JsonResponse
    {
        $dataModel = ($action !== 'create' && $id !== null) ? $this->model::find($id) : null;
        $viewData = $dataModel ? ['data' => $dataModel] : [];

        if (in_array($action, ['create', 'edit'])) {
            $viewData['condominiums'] = \App\Models\Condominiums::pluck('name', 'id');
        }

        return view($this->view . '.' . $action, $viewData);
    }
}
