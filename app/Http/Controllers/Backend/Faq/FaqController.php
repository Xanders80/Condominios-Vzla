<?php

namespace App\Http\Controllers\Backend\Faq;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
    /**
     * Muestra la vista de índice.
     *
     * Este método se encarga de retornar la vista principal del recurso.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->view . '.index');
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     *
     * Este método obtiene una lista de menús disponibles y la pasa a la vista de creación.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->handleViewAction('create');
    }

    /**
     * Muestra una entrada específica del modelo Faq.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->handleViewAction('show', $id);
    }

    /**
     * Muestra el formulario de edición para una entrada específica del modelo Faq.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->handleViewAction('edit', $id);
    }

    /**
     * Muestra la vista de confirmación de eliminación de un recurso.
     *
     * @param int $id el identificador del recurso a eliminar
     *
     * @return \Illuminate\View\View vista de confirmación de eliminación
     */
    public function delete($id)
    {
        return $this->handleViewAction('delete', $id);
    }

    /**
     * Almacena una nueva entrada en el modelo Faq.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->handleStoreOrUpdate($request, 'store');
    }

    /**
     * Actualiza un recurso existente en la base de datos.
     *
     * @param Request $request la solicitud HTTP que contiene los datos a actualizar
     * @param int     $id      el identificador del recurso a actualizar
     *
     * @return \Illuminate\Http\JsonResponse respuesta JSON con el resultado de la operación
     */
    public function update(Request $request, $id)
    {
        return $this->handleStoreOrUpdate($request, 'update', $id);
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
    public function data(Request $request)
    {
        try {
            // Obtener todos los datos del modelo
            $data = $this->model::all();

            // Retornar los datos en formato de datatables
            return datatables()->of($data)
                // Editar la columna 'publish' para mostrar un badge
                ->editColumn('publish', fn($data) => $this->getConciliatedIcon($data->publish))
                // Agregar columna de acción según permisos del usuario
                ->addColumn('action', fn($data) => "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id,$request->user(),$this->url)}</div>")
                // Agregar columna de índice
                ->addIndexColumn()
                // Permitir HTML en columnas específicas
                ->rawColumns(['action', 'publish'])
                // Devolver la respuesta en formato JSON
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fn(TowerSectorController) handleViewAction', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_DISPLAYING_MODEL')),
                'error' => $e->getMessage(),
                'data' => 'Data: ' . $data,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    private function getConciliatedIcon($isPublished)
    {
        return $isPublished
            ? config('constants.ICONS.VALID')
            : config('constants.ICONS.INVALID');
    }

    /**
     * Maneja el almacenamiento o actualización de una entrada en el modelo Faq.
     *
     * @param string   $action 'store' o 'update'
     * @param int|null $id     el ID de la entrada a actualizar (opcional)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleStoreOrUpdate(Request $request, string $action, ?string $id = null)
    {
        $status = false;
        $httpStatus = config('constants.STATUS_CODES.NOT_FOUND');
        $message = trans(config('constants.MESSAGES.MESS_BAD_REQUEST'));

        try {
            $validation = $this->validateRequestData($request, $action);
            if ($validation->fails()) {
                return $this->help::jsonResponse(false, $message, 400, $validation->errors()->toArray());
            }

            // Ejecutar la acción correspondiente
            if ($action === 'store') {
                $status = $this->storeFaq($request);
            } else {
                $status = $this->updateFaq($request, $id);
            }

            $message = $status ? trans(config('constants.MESSAGES.DATA_SUCCESS')) : trans(config('constants.MESSAGES.DATA_FAILED'));
            $httpStatus = $status ? config('constants.STATUS_CODES.OK') : config('constants.STATUS_CODES.NOT_FOUND');
        } catch (\Exception $e) {
            Log::error("Error en $action Faq: " . $e->getMessage());
        }

        return $this->help::jsonResponse($status, $message, $httpStatus);
    }

    /**
     * Valida los datos según la acción.
     */
    private function validateRequestData(Request $request, string $action)
    {
        return $action === 'store'
            ? $this->model::validationRulesStore($request->all())
            : $this->model::validationRulesUpdate($request->all());
    }

    /**
     * Crea una nueva entrada en el modelo Faq.
     */
    private function storeFaq(Request $request): bool
    {
        $data = $this->model::create($request->except('description'));
        if (!$data) {
            return false;
        }

        $this->handleDescriptionUpload($data, $request->description);
        $this->handleFileUpload($data, $request);

        return true;
    }

    /**
     * Actualiza una entrada existente en el modelo Faq.
     */
    private function updateFaq(Request $request, string $id): bool
    {
        $data = $this->model::findOrFail($id);

        $request->merge([
            'publish' => $request->has('publish') ? 1 : 0,
            'description' => $this->help::uploadImageBase64($request->description, $data),
        ]);

        if (!$data->update($request->all())) {
            return false;
        }

        $this->handleFileUpload($data, $request);

        return true;
    }

    /**
     * Maneja la actualización de la descripción con base64.
     */
    private function handleDescriptionUpload($data, $description)
    {
        $data->update(['description' => $this->help::uploadImageBase64($description, $data)]);
    }

    /**
     * Maneja la carga de archivos.
     */
    private function handleFileUpload($data, Request $request)
    {
        if ($request->hasFile('file')) {
            $data->file?->forceDelete(); // Eliminar archivo anterior si existe

            $data->file()->create([
                'data' => [
                    'disk' => config('filesystems.default'),
                    'target' => Storage::putFile($data->folder, $request->file('file')),
                    'name' => $request->file('file')->getClientOriginalName(),
                ],
            ]);
        }
    }

    /**
     * Elimina un recurso de la base de datos.
     *
     * @param int $id el identificador del recurso a eliminar
     *
     * @return \Illuminate\Http\JsonResponse respuesta JSON con el resultado de la operación
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
            Log::error('Error fn(TowerSectorController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
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
            Log::error('Error fn(TowerSectorController) handleViewAction', [
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
                $viewData['menu'] = Menu::pluck('title', 'id');
                break;
            case 'show':
            case 'delete':
                break;
            default:
                throw new \InvalidArgumentException("Invalid action: $action");
        }

        return $viewData;
    }
}
