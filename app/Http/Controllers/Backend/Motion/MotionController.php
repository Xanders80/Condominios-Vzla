<?php

namespace App\Http\Controllers\Backend\Motion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Motion\StoreMotionRequest;
use App\Http\Requests\Backend\Motion\UpdateMotionRequest;
use App\Models\AssemblySession;
use App\Services\Backend\MotionService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MotionController extends Controller
{
    protected $motionService;

    public function __construct(MotionService $motionService, Helper $helper)
    {
        parent::__construct($helper);
        $this->motionService = $motionService;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }
    public function store(StoreMotionRequest $request): JsonResponse
    {
        $response = $this->motionService->createMotion($request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }
    public function update(UpdateMotionRequest $request, $id): JsonResponse
    {
        $response = $this->motionService->updateMotion($id, $request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }
    public function destroy($id): JsonResponse
    {
        $response = $this->motionService->deleteMotion($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function data(Request $request): JsonResponse
    {
        $data = $this->model::with(['assemblySession'])->latest();
        return datatables()->of($data)
            ->editColumn('assembly_session_id', fn($d) => $d->assemblySession ? $d->assemblySession->session_date : 'N/A')
            ->editColumn('voting_type', fn($d) => ucfirst($d->voting_type))
            ->editColumn('status', function ($d) {
                $classes = ['proposed' => 'badge-light', 'open' => 'badge-info', 'approved' => 'badge-success', 'rejected' => 'badge-danger', 'closed' => 'badge-dark'];
                return '<span class="badge ' . ($classes[$d->status] ?? 'badge-secondary') . '">' . str_replace('_', ' ', ucfirst($d->status)) . '</span>';
            })
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
            $viewData['sessions'] = AssemblySession::whereIn('status', ['scheduled', 'in_progress'])->pluck('session_date', 'id');
        }
        return $viewData;
    }
}
