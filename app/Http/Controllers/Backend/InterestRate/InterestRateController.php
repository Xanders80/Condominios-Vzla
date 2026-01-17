<?php

namespace App\Http\Controllers\Backend\InterestRate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\InterestRate\StoreInterestRateRequest;
use App\Http\Requests\Backend\InterestRate\UpdateInterestRateRequest;
use App\Services\Backend\InterestRateService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InterestRateController extends Controller
{
    protected $rateService;

    public function __construct(InterestRateService $rateService, Helper $helper)
    {
        parent::__construct($helper);
        $this->rateService = $rateService;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }
    public function store(StoreInterestRateRequest $request): JsonResponse
    {
        $response = $this->rateService->createRate($request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }
    public function update(UpdateInterestRateRequest $request, $id): JsonResponse
    {
        $response = $this->rateService->updateRate($id, $request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }
    public function destroy($id): JsonResponse
    {
        $response = $this->rateService->deleteRate($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function data(Request $request): JsonResponse
    {
        $data = $this->model::latest();
        return datatables()->of($data)
            ->editColumn('percentage', fn($d) => number_format($d->percentage, 2) . ' %')
            ->editColumn('is_active', fn($d) => $d->is_active ? '<span class="badge badge-success">' . __('Active') . '</span>' : '<span class="badge badge-secondary">' . __('Inactive') . '</span>')
            ->addColumn('action', fn($d) => $this->help::generateActionButtons($d->id, $request->user(), $this->url, ['edit', 'delete']))
            ->addIndexColumn()
            ->rawColumns(['action', 'is_active'])
            ->make();
    }

    private function handleViewAction(string $action, ?string $id = null): View|JsonResponse
    {
        $dataModel = ($action !== 'create' && $id !== null) ? $this->model::find($id) : null;
        return view($this->view . '.' . $action, $dataModel ? ['data' => $dataModel] : []);
    }
}
