<?php

namespace App\Http\Controllers\Backend\FloorStreet;

use App\Http\Controllers\Controller;
use App\Models\TowerSector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FloorStreetController extends Controller
{
    /**
     * Muestra la vista principal del condominio.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->view . '.index');
    }

    /**
     * Muestra la vista para crear un nuevo condominio.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->handleViewAction('create');
    }

    /**
     * Almacena un nuevo grupo de acceso en la base de datos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->handleStoreOrUpdate($request, 'store');
    }

    /**
     * Muestra el formulario de edición para un registro específico.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        return $this->handleViewAction('edit', $id);
    }

    /**
     * Actualiza un registro específico en la base de datos.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        return $this->handleStoreOrUpdate($request, 'update', $id);
    }

    /**
     * Muestra el formulario de eliminación para un registro específico.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        return $this->handleViewAction('delete', $id);
    }

    /**
     * Obtiene y retorna los datos del modelo en formato datatables.
     *
     * Este método se encarga de obtener todos los registros del modelo
     * y los formatea para su uso en un datatable, incluyendo la
     * visualización de un badge para la columna 'publish' y
     * botones de acción según los permisos del usuario.
     *
     * @param Request $request la solicitud HTTP que contiene la información del usuario
     *
     * @return \Illuminate\Http\JsonResponse retorna una respuesta JSON con los datos formateados
     *                                       para el datatable, incluyendo las columnas de índice, acción y estado de publicación
     */
    public function data(Request $request): object
    {
        try {
            $data = $this->model::all();

            return datatables()->of($data)
                ->editColumn('tower_sector_id', function ($data) {
                    return $data->towerSector ? $data->towerSector->name : 'N/A';
                })
                ->addColumn('action', fn($data) => "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id,$request->user(),$this->url, ['edit', 'delete'])}</div>")
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make();
        } catch (\Exception $e) {
            Log::error('Error fn(FloorStreetController) handleViewAction', [
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
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
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
            Log::error('Error fn(FloorStreetController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja el almacenamiento o actualización de un grupo de acceso.
     *
     * @param string   $action 'store' o 'update'
     * @param int|null $id     el ID del grupo a actualizar (opcional)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleStoreOrUpdate(Request $request, string $action, ?string $id = null)
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
            Log::error('Error fn(FloorStreetController) handleStoreUpdate', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);
        }

        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja la visualización de un formulario de edición o eliminación de un grupo de acceso.
     *
     * @param int    $id     el ID del grupo a editar o eliminar
     * @param string $action 'edit' o 'delete'
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    private function handleViewAction(string $action, ?string $id = null)
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
            Log::error('Error fn(FloorStreetController) handleViewAction', [
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
                $viewData['towerSector'] = TowerSector::pluck('name', 'id');
                break;
            case 'delete':
                break;
            default:
                throw new \InvalidArgumentException("Invalid action: $action");
        }

        return $viewData;
    }
}
