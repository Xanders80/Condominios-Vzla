<?php

namespace App\Http\Controllers\Backend\IncidentReport;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\IncidentReport\StoreIncidentRequest;
use App\Http\Requests\Backend\IncidentReport\UpdateIncidentRequest;
use App\Models\Unit;
use App\Services\Backend\IncidentReportService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidentReportController extends Controller
{
    protected $incidentService;

    public function __construct(IncidentReportService $incidentService, Helper $helper)
    {
        parent::__construct($helper);
        $this->incidentService = $incidentService;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }
    public function store(StoreIncidentRequest $request): JsonResponse
    {
        $files = $request->file('attachments') ?? [];
        $response = $this->incidentService->createIncident($request->all(), $files);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }
    public function update(UpdateIncidentRequest $request, $id): JsonResponse
    {
        $files = $request->file('attachments') ?? [];
        $response = $this->incidentService->updateIncident($id, $request->all(), $files);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }
    public function destroy($id): JsonResponse
    {
        $response = $this->incidentService->deleteIncident($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function data(Request $request): JsonResponse
    {
        $data = $this->model::with(['unit'])->latest();
        return datatables()->of($data)
            ->editColumn('unit_id', fn($d) => $d->unit ? $d->unit->name : 'N/A')
            ->editColumn('priority', function ($d) {
                $classes = ['low' => 'badge-info', 'medium' => 'badge-primary', 'high' => 'badge-warning', 'critical' => 'badge-danger'];
                return '<span class="badge ' . ($classes[$d->priority] ?? 'badge-secondary') . '">' . ucfirst($d->priority) . '</span>';
            })
            ->editColumn('status', function ($d) {
                $classes = ['open' => 'badge-light', 'in_progress' => 'badge-info', 'resolved' => 'badge-success', 'closed' => 'badge-dark'];
                return '<span class="badge ' . ($classes[$d->status] ?? 'badge-secondary') . '">' . str_replace('_', ' ', ucfirst($d->status)) . '</span>';
            })
            ->addColumn('action', fn($d) => $this->help::generateActionButtons($d->id, $request->user(), $this->url, ['edit', 'delete']))
            ->addIndexColumn()
            ->rawColumns(['action', 'priority', 'status'])
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
            $viewData['units'] = Unit::pluck('name', 'id');
        }
        return $viewData;
    }
}
