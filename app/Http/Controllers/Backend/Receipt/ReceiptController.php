<?php

namespace App\Http\Controllers\Backend\Receipt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Receipt\StoreReceiptRequest;
use App\Http\Requests\Backend\Receipt\UpdateReceiptRequest;
use App\Models\Unit;
use App\Services\Backend\ReceiptService;
use App\Services\ReceiptPdfService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    protected $receiptService;
    protected $pdfService;

    public function __construct(ReceiptService $receiptService, ReceiptPdfService $pdfService, Helper $helper)
    {
        parent::__construct($helper);
        $this->receiptService = $receiptService;
        $this->pdfService = $pdfService;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }

    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }

    public function store(StoreReceiptRequest $request): JsonResponse
    {
        $response = $this->receiptService->createReceipt($request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code'], $response['errors'] ?? []);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }

    public function update(UpdateReceiptRequest $request, $id): JsonResponse
    {
        $response = $this->receiptService->updateReceipt($id, $request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code'], $response['errors'] ?? []);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }

    public function destroy($id): JsonResponse
    {
        $response = $this->receiptService->deleteReceipt($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function download($id)
    {
        $receipt = \App\Models\Receipt::findOrFail($id);
        return $this->pdfService->generateReceiptPdf($receipt)->download('recibo-' . $receipt->id . '.pdf');
    }

    public function data(Request $request): JsonResponse
    {
        try {
            $data = $this->model::with(['unit'])->latest();

            return datatables()->of($data)
                ->editColumn('unit_id', function ($data) {
                    return $data->unit ? $data->unit->name : 'N/A';
                })
                ->editColumn('amount_bs', function ($data) {
                    return number_format($data->amount_bs, 2, ',', '.') . ' Bs';
                })
                ->editColumn('amount_usd', function ($data) {
                    return '$ ' . number_format($data->amount_usd, 2);
                })
                ->editColumn('status', function ($data) {
                    $classes = [
                        'paid' => 'badge-success',
                        'pending' => 'badge-warning',
                        'partial' => 'badge-info',
                        'cancelled' => 'badge-danger',
                    ];
                    $class = $classes[$data->status] ?? 'badge-secondary';
                    return '<span class="badge ' . $class . '">' . trans(ucfirst($data->status)) . '</span>';
                })
                ->addColumn('action', function ($data) use ($request) {
                    $buttons = $this->help::generateActionButtons($data->id, $request->user(), $this->url, ['edit', 'delete']);
                    $downloadBtn = '<x-button-button class="btn btn-sm btn-outline pull-up"
                        onclick="window.open(\'' . route($this->url . '.download', $data->id) . '\', \'_blank\')"
                        title="' . trans('Download PDF') . '">
                        <span class="mdi mdi-file-pdf mdi-18px text-danger"></span>
                    </x-button-button>';
                    return "<div class='btn-group pull-up'>{$downloadBtn}{$buttons}</div>";
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->make();
        } catch (\Exception $e) {
            Log::error('Error fn(ReceiptController) data', [
                'error' => $e->getMessage(),
            ]);
            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    private function handleViewAction(string $action, ?string $id = null): View|JsonResponse
    {
        try {
            $dataModel = ($action !== 'create' && $id !== null) ? $this->model::find($id) : null;
            return view($this->view . '.' . $action, $this->prepareViewData($action, $dataModel));
        } catch (\Exception $e) {
            Log::error('Error fn(ReceiptController) handleViewAction', [
                'error' => $e->getMessage(),
            ]);
            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
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
