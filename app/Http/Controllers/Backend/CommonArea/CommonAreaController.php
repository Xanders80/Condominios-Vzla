<?php

namespace App\Http\Controllers\Backend\CommonArea;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\CommonArea\StoreCommonAreaRequest;
use App\Http\Requests\Backend\CommonArea\UpdateCommonAreaRequest;
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
            ->editColumn('status', function ($d) {
                $classes = ['active' => 'badge-success', 'under_maintenance' => 'badge-warning', 'closed' => 'badge-danger'];
                return '<span class="badge ' . ($classes[$d->status] ?? 'badge-secondary') . '">' . str_replace('_', ' ', ucfirst($d->status)) . '</span>';
            })
            ->editColumn('booking_fee', fn($d) => '$ ' . number_format($d->booking_fee, 2))
            ->addColumn('action', fn($d) => $this->help::generateActionButtons($d->id, $request->user(), $this->url, ['edit', 'delete']))
            ->addIndexColumn()
            ->rawColumns(['action', 'status'])
            ->make();
    }

    private function handleViewAction(string $action, ?string $id = null): View|JsonResponse
    {
        $dataModel = ($action !== 'create' && $id !== null) ? $this->model::find($id) : null;
        return view($this->view . '.' . $action, $dataModel ? ['data' => $dataModel] : []);
    }
}
