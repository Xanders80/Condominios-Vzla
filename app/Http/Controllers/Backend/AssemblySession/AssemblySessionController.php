<?php

namespace App\Http\Controllers\Backend\AssemblySession;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Assembly\StoreAssemblyRequest;
use App\Http\Requests\Backend\Assembly\UpdateAssemblyRequest;
use App\Services\Backend\AssemblyService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssemblySessionController extends Controller
{
    protected $assemblyService;

    public function __construct(AssemblyService $assemblyService, Helper $helper)
    {
        parent::__construct($helper);
        $this->assemblyService = $assemblyService;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }
    public function store(StoreAssemblyRequest $request): JsonResponse
    {
        $response = $this->assemblyService->createAssembly($request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }
    public function update(UpdateAssemblyRequest $request, $id): JsonResponse
    {
        $response = $this->assemblyService->updateAssembly($id, $request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }
    public function destroy($id): JsonResponse
    {
        $response = $this->assemblyService->deleteAssembly($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function data(Request $request): JsonResponse
    {
        $data = $this->model::latest();
        return datatables()->of($data)
            ->editColumn('session_type', fn($d) => ucfirst($d->session_type))
            ->editColumn('status', function ($d) {
                $classes = ['scheduled' => 'badge-primary', 'in_progress' => 'badge-info', 'completed' => 'badge-success', 'cancelled' => 'badge-danger'];
                return '<span class="badge ' . ($classes[$d->status] ?? 'badge-secondary') . '">' . str_replace('_', ' ', ucfirst($d->status)) . '</span>';
            })
            ->editColumn('quorum_percentage', fn($d) => $d->quorum_percentage . ' %')
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
