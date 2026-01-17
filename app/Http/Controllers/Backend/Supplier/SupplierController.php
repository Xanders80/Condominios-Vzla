<?php

namespace App\Http\Controllers\Backend\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Supplier\StoreSupplierRequest;
use App\Http\Requests\Backend\Supplier\UpdateSupplierRequest;
use App\Services\Backend\SupplierService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService, Helper $helper)
    {
        parent::__construct($helper);
        $this->supplierService = $supplierService;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }
    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $response = $this->supplierService->createSupplier($request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }
    public function update(UpdateSupplierRequest $request, string $id): JsonResponse
    {
        $response = $this->supplierService->updateSupplier($id, $request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }
    public function destroy($id): JsonResponse
    {
        $response = $this->supplierService->deleteSupplier($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function data(Request $request): JsonResponse
    {
        $data = $this->model::latest();
        return datatables()->of($data)
            ->editColumn('status', function ($d) {
                $class = $d->status === 'active' ? 'badge-success' : 'badge-secondary';
                return '<span class="badge ' . $class . '">' . ucfirst($d->status) . '</span>';
            })
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
