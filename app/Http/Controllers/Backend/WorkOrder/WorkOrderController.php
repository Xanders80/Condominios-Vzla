<?php

namespace App\Http\Controllers\Backend\WorkOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\WorkOrder\StoreWorkOrderRequest;
use App\Http\Requests\Backend\WorkOrder\UpdateWorkOrderRequest;
use App\Models\Supplier;
use App\Models\IncidentReport;
use App\Services\Backend\WorkOrderService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    protected $workOrderService;

    public function __construct(WorkOrderService $workOrderService, Helper $helper)
    {
        parent::__construct($helper);
        $this->workOrderService = $workOrderService;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }
    public function store(StoreWorkOrderRequest $request): JsonResponse
    {
        $files = $request->file('attachments') ?? [];
        $response = $this->workOrderService->createWorkOrder($request->all(), $files);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }
    public function update(UpdateWorkOrderRequest $request, $id): JsonResponse
    {
        $files = $request->file('attachments') ?? [];
        $response = $this->workOrderService->updateWorkOrder($id, $request->all(), $files);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }
    public function destroy($id): JsonResponse
    {
        $response = $this->workOrderService->deleteWorkOrder($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function data(Request $request): JsonResponse
    {
        $data = $this->model::with(['supplier', 'incidentReport'])->latest();
        return datatables()->of($data)
            ->editColumn('supplier_id', fn($d) => $d->supplier ? $d->supplier->name : 'N/A')
            ->editColumn('status', function ($d) {
                $classes = ['draft' => 'badge-light', 'assigned' => 'badge-primary', 'in_progress' => 'badge-info', 'completed' => 'badge-success', 'cancelled' => 'badge-danger'];
                return '<span class="badge ' . ($classes[$d->status] ?? 'badge-secondary') . '">' . str_replace('_', ' ', ucfirst($d->status)) . '</span>';
            })
            ->editColumn('estimated_cost', fn($d) => '$ ' . number_format($d->estimated_cost, 2))
            ->addColumn('action', fn($d) => $this->help::generateActionButtons($d->id, $request->user(), $this->url, ['edit', 'delete']))
            ->addIndexColumn()
            ->rawColumns(['action', 'status'])
            ->make();
    }

    private function handleViewAction(string $action, ?string $id = null): View|JsonResponse
    {
        $dataModel = ($action !== 'create' && $id !== null) ? $this->model::find($id) : null;
        return view($this->view . '.' . $action, $this->prepareViewData($action, $dataModel));
    }

    private function prepareViewData(string $action, $dataModel): array
    {
        $viewData = $dataModel ? ['data' => $dataModel] : [];
        if (in_array($action, ['create', 'edit'])) {
            $viewData['suppliers'] = Supplier::where('status', 'active')->pluck('name', 'id');
            $viewData['incidents'] = IncidentReport::whereIn('status', ['open', 'in_progress'])->pluck('title', 'id');
        }
        return $viewData;
    }
}
