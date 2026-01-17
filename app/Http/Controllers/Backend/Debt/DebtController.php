<?php

namespace App\Http\Controllers\Backend\Debt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Debt\StoreDebtRequest;
use App\Http\Requests\Backend\Debt\UpdateDebtRequest;
use App\Models\Unit;
use App\Models\Receipt;
use App\Services\Backend\DebtService;
use App\Services\InterestCalculationService;
use App\Services\JudicialCollectionReportService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DebtController extends Controller
{
    protected $debtService;
    protected $interestService;
    protected $judicialService;

    public function __construct(
        DebtService $debtService,
        InterestCalculationService $interestService,
        JudicialCollectionReportService $judicialService,
        Helper $helper
    ) {
        parent::__construct($helper);
        $this->debtService = $debtService;
        $this->interestService = $interestService;
        $this->judicialService = $judicialService;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }
    public function store(StoreDebtRequest $request): JsonResponse
    {
        $response = $this->debtService->createDebt($request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code'], $response['errors'] ?? []);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }
    public function update(UpdateDebtRequest $request, $id): JsonResponse
    {
        $response = $this->debtService->updateDebt($id, $request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code'], $response['errors'] ?? []);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }
    public function destroy($id): JsonResponse
    {
        $response = $this->debtService->deleteDebt($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function processInterests(): JsonResponse
    {
        $count = $this->interestService->processInterests();
        return $this->help::jsonResponse(true, "Processed $count interests", 200);
    }

    public function judicialReport($unitId)
    {
        return $this->judicialService->generateReport($unitId);
    }

    public function data(Request $request): JsonResponse
    {
        try {
            $data = $this->model::with(['unit', 'receipt'])->latest();
            return datatables()->of($data)
                ->editColumn('unit_id', fn($d) => $d->unit ? $d->unit->name : 'N/A')
                ->editColumn('amount', fn($d) => number_format($d->amount, 2, ',', '.') . ' Bs')
                ->editColumn('status', function ($d) {
                    $classes = ['current' => 'badge-info', 'delinquent' => 'badge-danger', 'paid' => 'badge-success', 'judicial' => 'badge-dark'];
                    return '<span class="badge ' . ($classes[$d->status] ?? 'badge-secondary') . '">' . trans(ucfirst($d->status)) . '</span>';
                })
                ->addColumn('action', function ($d) use ($request) {
                    $buttons = $this->help::generateActionButtons($d->id, $request->user(), $this->url, ['edit', 'delete']);
                    $judicialBtn = '<x-button-button class="btn btn-sm btn-outline pull-up"
                        onclick="window.open(\'' . route($this->url . '.judicial-report', $d->unit_id) . '\', \'_blank\')"
                        title="' . trans('Judicial Report') . '">
                        <span class="mdi mdi-gavel mdi-18px text-dark"></span>
                    </x-button-button>';
                    return "<div class='btn-group pull-up'>{$judicialBtn}{$buttons}</div>";
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->make();
        } catch (\Exception $e) {
            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), 500);
        }
    }

    private function handleViewAction(string $action, ?string $id = null): View|JsonResponse
    {
        try {
            $dataModel = ($action !== 'create' && $id !== null) ? $this->model::find($id) : null;
            return view($this->view . '.' . $action, $this->prepareViewData($action, $dataModel));
        } catch (\Exception $e) {
            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), 500);
        }
    }

    private function prepareViewData(string $action, $dataModel): array
    {
        $viewData = $dataModel ? ['data' => $dataModel] : [];
        if (in_array($action, ['create', 'edit'])) {
            $viewData['units'] = Unit::pluck('name', 'id');
            $viewData['receipts'] = Receipt::where('status', 'pending')->pluck('id', 'id');
        }
        return $viewData;
    }
}
