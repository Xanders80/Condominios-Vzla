<?php

namespace App\Http\Controllers\Backend\CommonExpense;

use App\Http\Controllers\Controller;
use App\Models\CommonExpense;
use Illuminate\Http\Request;

class CommonExpenseController extends Controller
{
    protected $expenseEngine;

    public string $model = CommonExpense::class;

    public string $url = 'common-expenses';

    public string $view = 'backend.common-expenses';

    public function __construct(\App\Services\Backend\ExpenseEngine $expenseEngine, \App\Support\Helper $helper)
    {
        parent::__construct($helper);
        $this->expenseEngine = $expenseEngine;
        $this->model = CommonExpense::class;
        $this->url = 'common-expenses';
        $this->view = 'backend.common-expenses';
    }

    /**
     * Display a listing of common expenses.
     */
    public function index(): \Illuminate\View\View
    {
        return $this->handleViewAction('index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        return $this->handleViewAction('create');
    }

    /**
     * Store a newly created common expense block.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'condominium_id' => 'required|exists:condominiums,id',
            'period' => 'required',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if (strlen($validated['period']) == 7) { // YYYY-MM
            $validated['period'] = $validated['period'].'-01';
        }

        if (CommonExpense::where('condominium_id', $validated['condominium_id'])->where('period', $validated['period'])->exists()) {
            return $this->help::jsonResponse(false, 'Ya existe un gasto registrado para este condominio en el periodo seleccionado.', 400);
        }

        $expense = CommonExpense::create($validated);

        return $this->help::jsonResponse(true, 'Gasto de periodo creado exitosamente.', 201, [], $expense->toArray());
    }

    /**
     * Trigger interest calculation and distribution for the period.
     */
    public function distribute(CommonExpense $common_expense)
    {
        $result = $this->expenseEngine->distribute($common_expense);

        if ($result['status']) {
            $common_expense->update(['status' => 'published']);
        }

        return $this->help::jsonResponse($result['status'], $result['message'], $result['status_code'], $result['errors'] ?? [], $result['data'] ?? []);
    }

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $this->model::with(['condominium'])->latest();

            return datatables()->of($data)
                ->editColumn('condominium_id', function ($data) {
                    return $data->condominium ? $data->condominium->name : 'N/A';
                })
                ->editColumn('period', function ($data) {
                    return $data->period ? $data->period->format('m/Y') : 'N/A';
                })
                ->editColumn('total_amount', function ($data) {
                    return number_format($data->total_amount, 2, ',', '.').' Bs';
                })
                ->editColumn('status', function ($data) {
                    $classes = [
                        'draft' => 'badge-warning',
                        'published' => 'badge-success',
                        'canceled' => 'badge-danger',
                    ];
                    $class = $classes[$data->status] ?? 'badge-secondary';

                    return '<span class="badge '.$class.'">'.trans(ucfirst($data->status)).'</span>';
                })
                ->addColumn('action', function ($data) use ($request) {
                    $requiredButtons = ($data->status === 'draft') ? ['show', 'edit', 'delete'] : ['show'];
                    $buttons = $this->help::generateActionButtons($data->id, $request->user(), $this->url, $requiredButtons);

                    $distributeBtn = '';
                    if ($data->status === 'draft') {
                        $distributeBtn = '<x-button-button class="btn btn-sm btn-outline pull-up"
                            onclick="handleRequest(\''.route($this->url.'.distribute', $data->id).'\', \'POST\', {}, null, true)"
                            title="'.trans('Distribute Aliquots').'">
                            <span class="mdi mdi-calculator mdi-18px text-primary"></span>
                        </x-button-button>';
                    }

                    return "<div class='btn-group pull-up'>{$distributeBtn}{$buttons}</div>";
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->make();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fn(CommonExpenseController) data', [
                'error' => $e->getMessage(),
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        return $this->handleViewAction('show', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $commonExpense = CommonExpense::find($id);
        if (! $commonExpense) {
            return $this->help::jsonResponse(false, 'Gasto no encontrado.', 404);
        }

        if ($commonExpense->status !== 'draft') {
            return $this->help::jsonResponse(false, 'Solo se pueden editar gastos en estado borrador.', 400);
        }

        $validated = $request->validate([
            'condominium_id' => 'required|exists:condominiums,id',
            'period' => 'required',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if (strlen($validated['period']) == 7) { // YYYY-MM
            $validated['period'] = $validated['period'].'-01';
        }

        if (CommonExpense::where('condominium_id', $validated['condominium_id'])
            ->where('period', $validated['period'])
            ->where('id', '!=', $id)
            ->exists()) {
            return $this->help::jsonResponse(false, 'Ya existe otro gasto registrado para este condominio en el periodo seleccionado.', 400);
        }

        $commonExpense->update($validated);

        return $this->help::jsonResponse(true, 'Gasto actualizado exitosamente.', 200, [], $commonExpense->toArray());
    }

    public function delete($id): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $commonExpense = CommonExpense::find($id);
        if (! $commonExpense) {
            return $this->help::jsonResponse(false, 'Gasto no encontrado.', 404);
        }

        if ($commonExpense->status === 'published') {
            return $this->help::jsonResponse(false, 'No se puede eliminar un gasto ya distribuido.', 400);
        }

        $commonExpense->delete();

        return $this->help::jsonResponse(true, 'Gasto eliminado.', 200);
    }

    private function handleViewAction(string $action, ?string $id = null): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        try {
            $dataModel = ($action !== 'create' && $id !== null) ? $this->model::find($id) : null;

            return view($this->view.'.'.$action, $this->prepareViewData($action, $dataModel));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fn(CommonExpenseController) handleViewAction', [
                'error' => $e->getMessage(),
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    private function prepareViewData(string $action, $dataModel): array
    {
        $viewData = $dataModel ? ['data' => $dataModel] : [];
        if (in_array($action, ['create', 'edit'])) {
            $viewData['condominiums'] = \App\Models\Condominiums::pluck('name', 'id');
        }

        return $viewData;
    }
}
