<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Booking\StoreBookingRequest;
use App\Http\Requests\Backend\Booking\UpdateBookingRequest;
use App\Models\Unit;
use App\Models\CommonArea;
use App\Services\Backend\BookingService;
use App\Support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService, Helper $helper)
    {
        parent::__construct($helper);
        $this->bookingService = $bookingService;
    }

    public function index(): View
    {
        return $this->handleViewAction('index');
    }
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create');
    }
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $response = $this->bookingService->createBooking($request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function edit($id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }
    public function update(UpdateBookingRequest $request, $id): JsonResponse
    {
        $response = $this->bookingService->updateBooking($id, $request->all());
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function delete($id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }
    public function destroy($id): JsonResponse
    {
        $response = $this->bookingService->deleteBooking($id);
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    public function data(Request $request): JsonResponse
    {
        $data = $this->model::with(['unit', 'commonArea'])->latest();
        return datatables()->of($data)
            ->editColumn('unit_id', fn($d) => $d->unit ? $d->unit->name : 'N/A')
            ->editColumn('common_area_id', fn($d) => $d->commonArea ? $d->commonArea->name : 'N/A')
            ->editColumn('status', function ($d) {
                $classes = ['pending' => 'badge-warning', 'confirmed' => 'badge-success', 'cancelled' => 'badge-danger', 'completed' => 'badge-info'];
                return '<span class="badge ' . ($classes[$d->status] ?? 'badge-secondary') . '">' . ucfirst($d->status) . '</span>';
            })
            ->editColumn('amount_paid', fn($d) => '$ ' . number_format($d->amount_paid, 2))
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
            $viewData['units'] = Unit::pluck('name', 'id');
            $viewData['areas'] = CommonArea::where('is_bookable', true)->pluck('name', 'id');
        }
        return $viewData;
    }
}
