<?php

namespace App\Http\Controllers\Backend\Payments;

use App\Http\Controllers\Controller;
use App\Models\Banks;
use App\Models\Condominiums;
use App\Models\Dweller;
use App\Models\WaysToPays;
use App\Services\PaymentsService;
use App\support\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentsController extends Controller
{
    protected $paymentsService;

    public function __construct(PaymentsService $paymentsService, Helper $helper)
    {
        parent::__construct($helper); // Pass the helper to the parent constructor
        $this->paymentsService = $paymentsService;
    }

    /**
     * Muestra la vista principal del condominio.
     *
     * @return View
     */
    public function index()
    {
        // Si no se proporcionan mes y año, usar el mes y año actual
        $month = now()->month;
        $years = $this->paymentsService->getYears(); // Almacena el resultado en una variable
        $year = reset($years);      // Obtén el primer año

        // Retornar la vista con los datos necesarios
        return view('backend.payments.index', $this->dataCards($month, $year));
    }

    /**
     * Muestra la vista para crear un nuevo condominio.
     */
    public function create(): View|JsonResponse
    {
        return $this->handleViewAction('create', null);
    }

    /**
     * Muestra el formulario de edición para un registro específico.
     *
     * @param string $id el ID del registro a editar
     */
    public function edit(string $id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }

    /**
     * Muestra el formulario de eliminación para un registro específico.
     *
     * @param string $id el ID del registro a eliminar
     */
    public function delete(string $id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }

    /**
     * Muestra una entrada específica del modelo Payment.
     *
     * @param string $id el ID del registro a mostrar
     */
    public function show(string $id): View|JsonResponse
    {
        return $this->handleViewAction('show', $id);
    }

    /**
     * Almacena un nuevo registro de Payment en la base de datos.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->handleStoreOrUpdate($request, 'store');
    }

    /**
     * Actualiza un registro específico de Payment en la base de datos.
     *
     * @param string $id el ID del registro a actualizar
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return $this->handleStoreOrUpdate($request, 'update', $id);
    }

    /**
     * Obtiene y retorna los datos del modelo en formato datatables.
     *
     * Este método se encarga de obtener todos los registros del modelo
     * y los formatea para su uso en un datatable, incluyendo
     * botones de acción según los permisos del usuario.
     *
     * @param Request $request la solicitud HTTP que contiene la información del usuario
     *
     * @return JsonResponse retorna una respuesta JSON con los datos formateados
     *                      para el datatable, incluyendo las columnas de índice, acción y estado de publicación
     */
    public function data(Request $request)
    {
        try {
            $month = $this->validateMonth($request->input('month'));
            $year = $this->validateYear($request->input('year'));

            // Obtener el mes y año actuales
            $currentMonth = date('m');
            $currentYear = date('Y');

            // Eager load dweller relationship
            $data = $this->model::with('dweller') // Cargar la relación dweller
                ->filterLevel()
                ->whereMonth('date_pay', $month)
                ->whereYear('date_pay', $year)
                ->get();

            return datatables()->of($data)
                ->editColumn('dweller_id', fn($data) => $data->dweller->name) // Asumiendo que tienes un método full_name en el modelo Dweller
                ->editColumn('conciliated', fn($data) => $this->getConciliatedIcon($data->conciliated))
                ->addColumn('action', function ($data) use ($month, $currentMonth, $year, $currentYear) {
                    if (($month == $currentMonth && $year == $currentYear) && !$data->conciliated) {
                        return "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id, request()->user(),$this->url)}</div>";
                    } else {
                        return "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id, request()->user(),$this->url, ['show'])}</div>";
                    }
                })
                ->addIndexColumn()
                ->rawColumns(['conciliated', 'action'])
                ->make();
        } catch (\Exception $e) {
            Log::error('Error fn(PaymentsController) handleViewAction', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_DISPLAYING_MODEL')),
                'error' => $e->getMessage(),
                'data' => 'Data: ' . $data,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    /**
     * Elimina un registro específico de la base de datos.
     *
     * @param int $id el ID del registro a eliminar
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $response = [];

            // Utiliza la función deleteData del modelo
            $response = $this->model::deleteData($id);
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = trans(config('constants.MESSAGES.DATA_DELETE_FAILED')) . trans(config('constants.MESSAGES.ERROR_TRYING_TO_DELETE_RESOURCE')) . ': ' . " $id: " . $e->getMessage();
            $response['status_code'] = config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR');
            Log::error('Error fn(PaymentsController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja la visualización de un formulario de creación, edición o eliminación de un Payment.
     *
     * @param string|null $id     El ID del registro, null para creación
     * @param string      $action 'create', 'edit', o 'delete'
     */
    private function handleViewAction(string $action, ?string $id): View|JsonResponse
    {
        try {
            $data = [];
            $dataUser = $action !== 'create'
                ? $this->model::findOrFail($id)
                : Dweller::where('email', auth()->user()->email)->first();

            if ($action !== 'create') {
                $data['data'] = $dataUser;
                $data['dweller'] = $dataUser->dweller->name;
            }

            if ($action === 'create' || $action === 'edit') {
                $data['banks'] = Banks::filterLevel()->pluck('nameBank', 'id');
                $data['condominiums'] = Condominiums::pluck('name', 'id');
                $data['waystopays'] = WaysToPays::pluck('name', 'id');
            }
            if ($action === 'create') {
                $data['dweller'] = $dataUser ? $dataUser->name : null;
                $data['dweller_id'] = $dataUser ? $dataUser->id : null;
            }

            return view($this->view . '.' . $action, $data);
        } catch (\Exception $e) {
            Log::error('Error fn(PaymentsController) handleViewAction', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_DISPLAYING_MODEL')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    /**
     * Maneja el almacenamiento o actualización de un registro de Payment.
     *
     * @param string      $action 'store' para crear, 'update' para actualizar
     * @param string|null $id     El ID del registro a actualizar, null para crear
     */
    private function handleStoreOrUpdate(Request $request, string $action, ?string $id = null): JsonResponse
    {
        try {
            $response = [];
            // Preparar los datos para la actualización
            $request->merge([
                'conciliated' => $request->has('conciliated') ? 1 : 0,
            ]);

            // Utiliza la función createData o updateData del modelo
            $response = $action === 'store' ? $this->model::createData($request->all()) : $this->model::updateData($id, $request->all());

            if (!$response['status']) {
                return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code'], $response['errors']);
            }
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = trans(config('constants.MESSAGES.DATA_ERROR')) . ' Condominiums ' . $action . ': ' . $e->getMessage();
            $response['status_code'] = config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR');
            Log::error('Error fn(CondominiumsController) handleStoreUpdate', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);
        }

        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Obtiene los datos de pagos para un mes y año específicos.
     *
     * Este método recupera información sobre los pagos realizados en un mes y año dados,
     * incluyendo el total acumulado, el número de pagos y el monto total.
     *
     * @param int|null $month el mes para el cual se desean obtener los datos (opcional)
     * @param int|null $year  el año para el cual se desean obtener los datos (opcional)
     *
     * @return array un arreglo que contiene el conteo de unidades, el año, el mes,
     *               el nombre del mes, los meses filtrados, los años disponibles,
     *               y los datos de pagos
     */
    public function dataCards(?int $month = null, ?int $year = null)
    {
        // Obtener meses y años
        $months = $this->help::getMonths();
        $years = $this->paymentsService->getYears();
        $availableMonths = $this->paymentsService->getMonthsForYear($year);

        // Filtrar los meses disponibles
        $filteredMonths = array_filter($months, fn($key) => in_array($key, $availableMonths), ARRAY_FILTER_USE_KEY);

        // Obtener el inquilino basado en el correo electrónico del usuario autenticado
        $dwellerID = $this->paymentsService->getDwellerID();
        $unitCount = $this->paymentsService->getUnitCount();

        // Obtener el nombre del mes anterior
        $monthName = now()->setMonth($month)->locale('es')->monthName;

        // Datos para la vista
        $data = $this->paymentsService->getDataCards($year, $dwellerID, $month);

        return compact('unitCount', 'year', 'month', 'monthName', 'filteredMonths', 'years', 'data');
    }

    /**
     * Obtiene los meses disponibles para un año específico.
     *
     * @return JsonResponse
     */
    public function getMonthsForYearJson(int $year)
    {
        $months = $this->paymentsService->getMonthsForYear($year);

        return response()->json($months);
    }

    private function validateMonth($month)
    {
        return ($month < 1 || $month > 12) ? date('m') : $month;
    }

    private function validateYear($year)
    {
        return ($year < 2000 || $year > 2100) ? date('Y') : $year;
    }

    private function getConciliatedIcon($isConciliated)
    {
        return $isConciliated
            ? config('constants.ICONS.VALID')
            : config('constants.ICONS.INVALID');
    }
}
