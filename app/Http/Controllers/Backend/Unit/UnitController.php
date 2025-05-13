<?php

namespace App\Http\Controllers\Backend\Unit;

use App\Http\Controllers\Controller;
use App\Models\Dweller;
use App\Models\FloorStreet;
use App\Models\TowerSector;
use App\Models\UnitType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UnitController extends Controller
{
    /**
     * Muestra la vista principal del condominio.
     *
     * @return View
     */
    public function index()
    {
        return $this->handleViewAction('index');
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
     * Almacena un nuevo grupo de acceso en la base de datos.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->handleStoreOrUpdate($request, 'store');
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
     * Actualiza un registro específico en la base de datos.
     *
     * @param int $id el ID del registro a actualizar
     */
    public function update(Request $request, $id): JsonResponse
    {
        return $this->handleStoreOrUpdate($request, 'update', $id);
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
            // Eager load las relaciones 'unitType', 'dweller', 'towerSector' y 'floorStreet'
            $data = $this->model::with(['unitType', 'dweller', 'towerSector', 'floorStreet'])->filterLevel();

            return datatables()->of($data)
                ->editColumn('unit_type_id', function ($data) {
                    // Acceder al nombre del tipo de unidad directamente
                    return $data->unitType ? $data->unitType->name : 'N/A';
                })
                ->editColumn('dweller_name', function ($data) {
                    // Acceder al nombre del inquilino directamente
                    return $data->dweller ? $data->dweller->name : 'N/A';
                })
                ->filterColumn('dweller_name', function ($query, $keyword) {
                    $query->whereHas('dweller', function ($q) use ($keyword) {
                        $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
                    });
                })
                ->editColumn('tower_sector_id', function ($data) {
                    // Acceder al nombre del sector de la torre directamente
                    return $data->towerSector ? $data->towerSector->name : 'N/A';
                })
                ->editColumn('floor_street_id', function ($data) {
                    // Acceder al nombre de la calle del piso directamente
                    return $data->floorStreet ? $data->floorStreet->name : 'N/A';
                })
                // Editar la columna 'Estado' para mostrar un badge
                ->editColumn('status', function ($data) {
                    return $data->status
                        ? '<span class="badge badge-success">' . trans('Inhabited') . '</span>'
                        : '<span class="badge badge-danger">' . trans('Uninhabited') . '</span>';
                })
                ->addColumn('action', fn($data) => "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id,$request->user(),$this->url, ['edit', 'delete'])}</div>")
                ->addIndexColumn()
                // Permitir HTML en columnas específicas
                ->rawColumns(['action', 'status'])
                ->make();
        } catch (\Exception $e) {
            Log::error('Error fn(UnitController) handleViewAction', [
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
            Log::error('Error fn(UnitController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja el almacenamiento o actualización de un registro de Unit.
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
                'status' => $request->has('status') ? 1 : 0,
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
            Log::error('Error fn(UnitController) handleStoreUpdate', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);
        }

        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja la visualización de un formulario de edición o eliminación de una Unit.
     *
     * @param int    $id
     * @param string $action 'edit' para editar, 'delete' para eliminar
     */
    private function handleViewAction(string $action, ?string $id = null): View|JsonResponse
    {
        try {
            // Inicializar $dataModel como null
            $dataModel = null;

            // Solo buscar el registro si la acción no es 'create' y el ID no es null
            if ($action !== 'create' && $id !== null) {
                $dataModel = $this->model::find($id); // Usar find en lugar de findOrFail
            }

            // Preparar los datos y retornar a la vista según la acción
            return view($this->view . '.' . $action, $this->prepareViewData($action, $dataModel));
        } catch (\Exception $e) {
            Log::error('Error fn(UnitController) handleViewAction', [
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
        $viewData = [];

        // Agregar $data solo si no es null
        if ($dataModel !== null) {
            $viewData['data'] = $dataModel;
        }

        switch ($action) {
            case 'create':
            case 'edit':
                $viewData['unitTypes'] = UnitType::pluck('name', 'id');
                $viewData['dweller'] = Dweller::filterLevel()->get()
                    ->mapWithKeys(function ($item) {
                        return [$item->id => $item->first_name . ' ' . $item->last_name];
                    });
                $viewData['towerSector'] = TowerSector::pluck('name', 'id');
                $viewData['floorStreet'] = FloorStreet::pluck('name', 'id');
                break;
            case 'delete':
                break;
            case 'index':
                // Inicializar el conteo de registros
                $viewData['recordCount'] = 0;

                $viewData['dweller'] = Dweller::where('email', auth()->user()->email)->get()->map(function ($item) {
                    return $item->first_name . ' ' . $item->last_name; // Concatenar first_name y last_name
                })->first();

                // Verificar si el usuario no es de nivel user
                if (auth()->user()->level->code === 'user' && $viewData['dweller']) {
                    // Contar el número de registros en la tabla
                    $viewData['recordCount'] = $this->model::where('dweller_id', Dweller::where('email', auth()->user()->email)->value('id'))->count();
                }
                break;
            default:
                throw new \InvalidArgumentException("Invalid action: $action");
        }

        return $viewData;
    }
}
