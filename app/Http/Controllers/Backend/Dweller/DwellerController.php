<?php

namespace App\Http\Controllers\Backend\Dweller;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\DwellerType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DwellerController extends Controller
{
    /**
     * Muestra la vista principal del condominio.
     *
     * @return View
     */
    public function index()
    {
        $totalUsers = User::whereNotNull('email_verified_at')->count();

        // Obtener registros de dwellers y agrupar por tipo
        $dwellerCounts = $this->model::selectRaw('dweller_type_id, count(*) as count')
            ->groupBy('dweller_type_id')
            ->pluck('count', 'dweller_type_id');

        $dwellerTypes = DwellerType::pluck('name', 'id');
        $dwellerRegisters = $dwellerCounts->sum();

        $dataDweller = $this->getDataDweller($totalUsers, $dwellerRegisters, $dwellerCounts, $dwellerTypes);

        // Obtener el conteo de registros según el nivel del usuario
        $user = auth()->user();
        $recordCount = $user->level->code === 'user'
            ? $this->model::where('email', $user->email)->count()
            : 0;

        return view("{$this->view}.index", compact('recordCount', 'dataDweller'));
    }

    /**
     * Genera los datos de dwellers para la vista.
     *
     * @param int                            $totalUsers
     * @param int                            $dwellerRegisters
     * @param \Illuminate\Support\Collection $dwellerCounts
     * @param \Illuminate\Support\Collection $dwellerTypes
     *
     * @return array
     */
    private function getDataDweller($totalUsers, $dwellerRegisters, $dwellerCounts, $dwellerTypes)
    {
        $data = [
            [
                'label' => trans('Users'),
                'message' => $totalUsers,
                'sub_message' => '100',
                'end_text' => trans('Verified'),
                'icon' => 'mdi mdi-account-check mdi-36px',
            ],
            [
                'label' => trans('Dwellers'),
                'message' => $dwellerRegisters,
                'sub_message' => $this->calculatePercentage($dwellerRegisters, $totalUsers),
                'end_text' => trans('Registered'),
                'icon' => 'mdi mdi-account-star mdi-flip-h mdi-36px',
            ],
        ];

        // Generar datos para cada tipo de dweller
        foreach ($dwellerTypes as $id => $name) {
            $count = $dwellerCounts[$id] ?? 0;
            $data[] = [
                'label' => trans($name),
                'message' => $count,
                'sub_message' => $this->calculatePercentage($count, $dwellerRegisters),
                'end_text' => trans('Registered'),
                'icon' => 'mdi mdi-account-key mdi-36px mdi-flip-h',
            ];
        }

        return $data;
    }

    /**
     * Muestra la vista para crear un nuevo condominio.
     *
     * @return View
     */
    public function create()
    {
        return $this->handleViewAction('create');
    }

    /**
     * Muestra los detalles de un grupo de acceso específico.
     *
     * @param int $id
     *
     * @return View|JsonResponse
     */
    public function show($id)
    {
        return $this->handleViewAction('show', $id);
    }

    /**
     * Muestra el formulario de edición para un registro específico.
     *
     * @param int $id el ID del registro a editar
     */
    public function edit(string $id): View|JsonResponse
    {
        return $this->handleViewAction('edit', $id);
    }

    /**
     * Muestra el formulario de eliminación para un registro específico.
     *
     * @param int $id el ID del registro a eliminar
     */
    public function delete(string $id): View|JsonResponse
    {
        return $this->handleViewAction('delete', $id);
    }

    /**
     * Almacena un nuevo grupo de acceso en la base de datos.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->handleStoreOrUpdate($request, 'store');
    }

    /**
     * Actualiza un registro específico en la base de datos.
     *
     * @param int $id el ID del registro a actualizar
     */
    public function update(Request $request, $id): JsonResponse
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
            // Eager load las relaciones 'documentType' y 'dwellerType'
            $data = $this->model::with(['documentType', 'dwellerType'])->filterLevel();

            return datatables()->of($data)
                ->filterColumn('name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
                })
                ->editColumn('document_type_id', function ($data) {
                    // Acceder al nombre del tipo de documento directamente
                    return $data->documentType ? $data->documentType->name : 'N/A';
                })
                ->editColumn('dweller_type_id', function ($data) {
                    // Acceder al nombre del tipo de inquilino directamente
                    return $data->dwellerType ? $data->dwellerType->name : 'N/A';
                })
                ->addColumn('action', fn($data) => "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id,$request->user(),$this->url)}</div>")
                ->addIndexColumn()
                // Permitir HTML en columnas específicas
                ->rawColumns(['action'])
                ->make();
        } catch (\Exception $e) {
            Log::error('Error fn(DwellerController) handleViewAction', [
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
            Log::error('Error fn(DwellerController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Almacena o actualiza un registro de Dweller.
     *
     * @param string      $action 'store' para crear, 'update' para actualizar
     * @param string|null $id     El ID del registro a actualizar, null para crear
     */
    private function handleStoreOrUpdate(Request $request, string $action, ?string $id = null): JsonResponse
    {
        try {
            $response = [];
            // Utiliza la función createData o updateData del modelo
            $response = $action === 'store' ? $this->model::createData($request->all()) : $this->model::updateData($id, $request->all());

            if (!$response['status']) {
                return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code'], $response['errors']);
            }
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = trans(config('constants.MESSAGES.DATA_ERROR')) . ' Condominiums ' . $action . ': ' . $e->getMessage();
            $response['status_code'] = config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR');
            Log::error('Error fn(DwellerController) handleStoreUpdate', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);
        }

        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja la visualización de un formulario de edición o eliminación de un Dweller.
     *
     * @param string      $action 'edit' para editar, 'delete' para eliminar, 'create' para crear
     * @param string|null $id     El ID del registro a editar o eliminar, null para crear
     */
    private function handleViewAction(string $action, ?string $id = null): View|JsonResponse
    {
        try {
            $dataModel = $id ? $this->model::find($id) : null;

            return view($this->view . '.' . $action, $this->prepareViewData($action, $dataModel));
        } catch (\Exception $e) {
            Log::error('Error fn(DwellerController) handleViewAction', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_DISPLAYING_MODEL')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    /**
     * Prepara los datos necesarios para la vista según la acción.
     *
     * @param string $action    La acción a realizar ('show', 'edit', 'delete', 'create')
     * @param mixed  $dataModel El modelo de datos (puede ser null para 'create')
     */
    private function prepareViewData(string $action, $dataModel): array
    {
        $viewData = $dataModel ? ['data' => $dataModel] : [];

        switch ($action) {
            case 'edit':
                $viewData['idType'] = DocumentType::pluck('name', 'id');
                $viewData['dwellerType'] = DwellerType::pluck('name', 'id');
                break;
            case 'create':
                $viewData = array_merge($viewData, [
                    'idType' => DocumentType::pluck('name', 'id'),
                    'listEmail' => User::pluck('email', 'email'),
                    'userLevel' => auth()->user()->level->code,
                    'dwellerType' => DwellerType::pluck('name', 'id'),
                    'data' => User::where('email', auth()->user()->email)->first(),
                ]);
                break;
            case 'show':
            case 'delete':
                break;
            default:
                throw new \InvalidArgumentException("Invalid action: $action");
        }

        return $viewData;
    }

    private function calculatePercentage($part, $total)
    {
        return $total > 0 ? round(($part / $total) * 100) : 0;
    }
}
