<?php

namespace App\Http\Controllers\Backend\Announcement;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador para manejar las operaciones de anuncios.
 */
class AnnouncementController extends Controller
{
    /**
     * Muestra la vista de índice de anuncios.
     */
    public function index(): object
    {
        return view($this->view . '.index');
    }

    /**
     * Muestra la vista para crear un nuevo anuncio.
     */
    public function create(): object
    {
        return $this->handleViewAction('create');
    }

    /**
     * Almacena un nuevo anuncio en la base de datos.
     *
     * @param Request $request la solicitud que contiene los datos del anuncio
     *
     * @return object respuesta JSON que indica el estado de la operación
     */
    public function store(Request $request): object
    {
        return $this->handleStoreOrUpdate($request, 'store');
    }

    /**
     * Muestra un anuncio específico.
     *
     * @param int $id el ID del anuncio a mostrar
     *
     * @return object vista del anuncio
     */
    public function show(string $id): object
    {
        return $this->handleViewAction('show', $id);
    }

    /**
     * Muestra el formulario de edición para un anuncio específico.
     *
     * @param int $id el ID del anuncio a editar
     *
     * @return object vista del formulario de edición del anuncio
     */
    public function edit($id): object
    {
        return $this->handleViewAction('edit', $id);
    }

    /**
     * Actualiza un anuncio existente.
     *
     * @param Request $request solicitud que contiene los datos a actualizar
     * @param int     $id      ID del anuncio a actualizar
     *
     * @return object respuesta en formato JSON con el estado y mensaje de la operación
     */
    public function update(Request $request, string $id): object
    {
        return $this->handleStoreOrUpdate($request, 'update', $id);
    }

    /**
     * Muestra el formulario de eliminación para un registro específico.
     *
     * @param int $id el ID del registro a eliminar
     *
     * @return \Illuminate\View\View
     */
    public function delete($id)
    {
        return $this->handleViewAction('delete', $id);
    }

    /**
     * Muestra los detalles de un anuncio.
     *
     * @param int    $id    ID del anuncio a mostrar
     * @param string $title título del anuncio
     *
     * @return object vista de detalles del anuncio
     */
    public function detail(string $id, string $title): object
    {
        return $this->handleViewAction('detail', $id, $title);
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
            // Obtener todos los datos del modelo
            $data = $this->model::with('menu');

            // Retornar los datos en formato de datatables
            return datatables()->of($data)
                // Editar la columna 'urgency' para mostrar un badge
                ->editColumn('urgency', function ($data) {
                    return config('master.content.announcement.status')[$data->urgency];
                })
                // Editar la columna 'publish' para mostrar un badge
                ->editColumn('publish', function ($data) {
                    return $data->publish
                        ? '<i class="mdi mdi-checkbox-marked-circle-outline mdi-18px text-success"></i>' // Icono de activo
                        : '<i class="mdi mdi-close-circle-outline mdi-18px text-danger"></i>'; // Icono de inactivo
                })
                // Agregar columna de acción según permisos del usuario
                ->addColumn('action', fn($data) => "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id,$request->user(),$this->url)}</div>")
                // Agregar columna de índice
                ->addIndexColumn()
                // Permitir HTML en columnas específicas
                ->rawColumns(['action', 'publish', 'urgency'])
                // Devolver la respuesta en formato JSON
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fn(AnnouncementController) handleViewAction', [
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
            Log::error('Error fn(AnnouncementController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Extrae y guarda archivos asociados a un anuncio.
     *
     * @param Request $request      solicitud que contiene los archivos
     * @param mixed   $announcement anuncio al que se asociarán los archivos
     */
    public function extracted(Request $request, $announcement): void
    {
        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $announcement->file()->create([
                    'data' => [
                        'name' => $file->getClientOriginalName(),
                        'disk' => config('filesystems.default'),
                        'target' => Storage::disk(config('filesystems.default'))->putFile($this->code . '/' . date('Y') . '/' . date('m') . '/' . date('d'), $file),
                    ],
                ]);
            }
        }
    }

    /**
     * Maneja el almacenamiento o actualización de un anuncio.
     *
     * @param string   $action 'store' o 'update'
     * @param int|null $id     el ID del anuncio a actualizar (opcional)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleStoreOrUpdate(Request $request, string $action, ?string $id = null)
    {
        $status = false;
        $message = trans(config('constants.MESSAGES.MESS_BAD_REQUEST'));
        $httpStatus = config('constants.STATUS_CODES.NOT_FOUND');

        try {
            // Validar los datos
            $validation = $action === 'store'
                ? $this->model::validationRulesStore($request->all())
                : $this->model::validationRulesUpdate($request->all());

            if ($validation->fails()) {
                return $this->help::jsonResponse($status, $message, $httpStatus, $validation->errors()->toArray());
            }

            // Limpiar el contenido de scripts
            $request->merge([
                'content' => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->get('content')),
                'publish' => $request->has('publish') ? 1 : 0,
            ]);

            if ($action === 'store') {
                $announcement = $this->model::create($request->all());
                if ($announcement) {
                    $this->extracted($request, $announcement);
                    $users = $request->user()->all_user_id;

                    // Enviar notificación a los usuarios
                    $this->help::sendNotification($announcement, $users, [
                        'title' => trans('New Announcement'),
                        'link' => $announcement->link,
                        'icon' => 'fa fa-bullhorn',
                        'color' => 'text-info',
                        'content' => $announcement->title,
                    ]);

                    $status = true;
                }
            } elseif ($action === 'update') {
                $data = $this->model::findOrFail($id);
                $status = $data->update($request->all()) ? true : false;
            }

            $message = $status ? trans(config('constants.MESSAGES.DATA_SUCCESS')) : trans(config('constants.MESSAGES.DATA_FAILED'));
            $httpStatus = $status ? config('constants.STATUS_CODES.OK') : config('constants.STATUS_CODES.NOT_FOUND');
        } catch (\Exception $e) {
            $errorMessage = trans($message . 'Announcement handleStoreOrUpdate:') . $e->getMessage();
            Log::error($errorMessage);

            return $this->help::jsonResponse(false, $errorMessage, config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }

        return $this->help::jsonResponse($status, $message, $httpStatus);
    }

    /**
     * Maneja la visualización, edición, eliminación y detalles de un anuncio específico.
     *
     * @param int         $id     el ID del anuncio
     * @param string      $action 'show', 'edit', 'delete' o 'detail'
     * @param string|null $title  título del anuncio (solo para la acción 'detail')
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    private function handleViewAction(string $action, ?string $id = null, ?string $title = null)
    {
        try {
            // Inicializar $data como null
            $data = null;

            // Solo buscar el registro si la acción no es 'create'
            if ($action !== 'create') {
                $data = $this->model::findOrFail($id); // Buscar el anuncio por ID
            }

            // Preparar los datos y retornar a la vista según la acción
            return view($this->view . '.' . $action, $this->prepareViewData($action, $data, $title));
        } catch (\Exception $e) {
            Log::error('Error fn(AnnouncementController) handleViewAction', [
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
     * @param string      $action La acción a realizar ('create', 'show', 'edit', 'delete', 'detail')
     * @param mixed       $data   Los datos principales del modelo
     * @param string|null $title  El título para la acción 'detail'
     */
    private function prepareViewData(string $action, $data, ?string $title = null): array
    {
        $viewData = [];

        // Agregar $data solo si no es null
        if ($data !== null) {
            $viewData['data'] = $data;
        }

        switch ($action) {
            case 'create':
            case 'edit':
                $viewData['menu'] = Menu::pluck('title', 'id');
                $viewData['parent'] = $this->model::pluck('title', 'id');
                break;
            case 'detail':
                $viewData['title'] = $title;
                break;
            case 'show':
            case 'delete':
                // No se necesitan datos adicionales para estas acciones
                break;
            default:
                throw new \InvalidArgumentException("Invalid action: $action");
        }

        return $viewData;
    }
}
