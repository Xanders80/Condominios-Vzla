<?php

namespace App\Http\Controllers\Backend\Condominiums;

use App\Http\Controllers\Controller;
use App\Models\Dweller;
use App\Models\PostalCodeAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CondominiumsController extends Controller
{
    /**
     * Muestra la vista principal del condominio.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->handleViewAction('index');
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
        return $this->handleStoreUpdate($request, 'store');
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
        return $this->handleStoreUpdate($request, 'update', $id);
    }

    /**
     * Muestra los detalles de un grupo de acceso específico.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->handleViewAction('show', $id);
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
                ->editColumn('active', fn($data) => $this->getActiveIcon($data->active))
                ->addColumn('action', fn($data) => "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id,$request->user(),$this->url)}</div>")
                ->addIndexColumn()
                ->rawColumns(['action', 'active'])
                ->make();
        } catch (\Exception $e) {
            Log::error('Error fn(CondominiumsController) handleViewAction', [
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
            Log::error('Error fn(CondominiumsController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja el almacenamiento y actualización de registros.
     *
     * @param int|null $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleStoreUpdate(Request $request, string $action, ?string $id = null)
    {
        try {
            $response = [];
            // Preparar los datos para la actualización
            $request->merge([
                'active' => $request->has('active') ? 1 : 0,
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
     * Maneja la lógica común para mostrar, editar o eliminar un registro.
     *
     * @param int    $id     el ID del registro
     * @param string $action la acción a realizar (show, edit, delete)
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
            // Manejo de errores
            Log::error('Error fn(CondominiumsController) handleViewAction', [
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
            case 'index':
                $viewData = [
                    'dweller' => !empty(trim(Dweller::getFirstDweller()->name)),
                ];
                break;
            case 'show':
                $viewData = [
                    'fullAddress' => $this->help::getAddressById($dataModel->postal_code_address),
                    'nameActivo' => $dataModel->active ? trans('Yes') : trans('No'),
                    'data' => $dataModel, // Aquí se define 'data'
                ];
                break;
            case 'edit':
                $response = $this->getAddressByZone($dataModel->address_line);
                $fullAddress = $response instanceof \Illuminate\Http\JsonResponse
                    ? json_decode($response->getContent(), true)
                    : [];
                $viewData = [
                    'fullAddress' => $fullAddress,
                    'data' => $dataModel, // Aquí se define 'data'
                ];
                break;
            case 'delete':
                $viewData = [
                    'data' => $dataModel, // Aquí se define 'data' en lugar de usar compact('dataModel')
                ];
                break;
            case 'create':
                $viewData = [
                    'nameInCharge' => Dweller::getFirstDweller()->name,
                ];
                break;
            default:
                throw new \InvalidArgumentException("Invalid action: $action");
        }

        return $viewData;
    }

    /**
     * Obtiene direcciones por zona basado en un término de búsqueda.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAddressByZone(string $searchTerm)
    {
        try {
            $postalCodes = PostalCodeAddress::where('postal_zone.name', 'LIKE', '%' . $searchTerm . '%')
                ->selectRaw('postal_zone.id, CONCAT(' . self::getAddressConcatString() . ') AS data')
                ->join('parishes', 'postal_zone.parish_id', '=', 'parishes.id')
                ->join('municipalities', 'parishes.municipality_id', '=', 'municipalities.id')
                ->join('cities', 'municipalities.id', '=', 'cities.municipality_id')
                ->join('states', 'cities.state_id', '=', 'states.id')
                ->groupBy(
                    'postal_zone.id',
                    'postal_zone.name',
                    'parishes.name',
                    'municipalities.name',
                    'states.name',
                    'postal_zone.zip_code'
                )
                ->pluck('data', 'postal_zone.id');

            if ($postalCodes->isEmpty()) {
                return response()->json(['data' => 'No se encontraron direcciones, verifique su búsqueda.'], 404);
            }

            return response()->json($postalCodes);
        } catch (\Exception $e) {
            Log::error('Error fn(CondominiumsController) getAddressByZone', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => $searchTerm,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    protected static function getAddressConcatString()
    {
        return "postal_zone.name, ', Parroquia ',
              parishes.name, ', Ciudad ',
              MAX(cities.name), ', Municipio ',
              municipalities.name, ', Estado ',
              states.name, ', VE, Código Postal: ',
              postal_zone.zip_code";
    }

    protected static function getActiveIcon($isActive)
    {
        return $isActive
            ? config('constants.ICONS.VALID')
            : config('constants.ICONS.INVALID');
    }
}
