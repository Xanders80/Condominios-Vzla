<?php

namespace App\Http\Controllers\Backend\Menu;

use App\Http\Controllers\Controller;
use App\Models\AccessGroup;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    /**
     * Muestra la vista principal del menu.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->view . '.index');
    }

    /**
     * Muestra la vista para crear un nuevo grupo de acceso.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->handleViewAction('create', null);
    }

    /**
     * Muestra el formulario de edición para un registro específico.
     *
     * @param int $id el ID del registro a editar
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return $this->handleViewAction('edit', $id);
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
     * Almacena un nuevo grupo de acceso en la base de datos.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        return $this->handleStoreOrUpdate($request, 'store');
    }

    /**
     * Actualiza un registro específico en la base de datos.
     *
     * @param int $id el ID del registro a actualizar
     *
     * @return JsonResponse
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
     * @return JsonResponse retorna una respuesta JSON con los datos formateados
     *                      para el datatable, incluyendo las columnas de índice, acción y estado de publicación
     */
    public function data()
    {
        $menu = $this->model::with(['children'])->whereNull('parent_id')->sort()->get();

        return view($this->view . '.list-menu.list-menu', compact('menu'));
    }

    /**
     * Maneja el almacenamiento o actualización de un registro de Menu.
     *
     * @param string   $action 'store' para crear, 'update' para actualizar
     * @param int|null $id     El ID del registro a actualizar, null para crear
     */
    private function handleStoreOrUpdate(Request $request, string $action, ?string $id = null): JsonResponse
    {
        $status = false;
        $httpStatus = config('constants.STATUS_CODES.BAD_REQUEST');
        $message = trans(config('constants.MESSAGES.MESS_BAD_REQUEST'));

        try {
            $validation = $this->validateRequestData($request, $action, $id);
            if ($validation->fails()) {
                return $this->help::jsonResponse($status, $message, $httpStatus, $validation->errors()->toArray());
            }

            // Ejecutar la acción correspondiente
            $status = ($action === 'store')
                ? $this->storeMenu($request)
                : $this->updateMenu($request, $id);

            // Configurar respuesta
            $message = $status ? trans(config('constants.MESSAGES.DATA_SUCCESS')) : trans(config('constants.MESSAGES.DATA_FAILED'));
            $httpStatus = $status ? config('constants.STATUS_CODES.OK') : config('constants.STATUS_CODES.NOT_FOUND');
        } catch (\Exception $e) {
            Log::error("Error en $action Menu: " . $e->getMessage());
            $message = trans(config('constants.MESSAGES.DATA_FAILED'));
            $httpStatus = config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR');
        }

        return $this->help::jsonResponse($status, $message, $httpStatus);
    }

    /**
     * Valida los datos según la acción.
     */
    private function validateRequestData(Request $request, string $action, ?string $id)
    {
        return $action === 'store'
            ? $this->model::validationRulesStore($request->all())
            : $this->model::validationRulesUpdate($request->all(), $id);
    }

    /**
     * Crea un nuevo registro de menú.
     */
    private function storeMenu(Request $request): bool
    {
        $data = $this->model::create($request->all());

        return !$data ? $this->handleAccessMenu($data, $request->access_group_id, $request) : false;
    }

    /**
     * Actualiza un registro existente de menú.
     */
    private function updateMenu(Request $request, string $id): bool
    {
        $data = $this->model::find($id);
        if (!$data || !$data->update($request->all())) {
            return false;
        }

        $data->access_menu()->delete();

        return $this->handleAccessMenu($data, $request->access_group_id, $request);
    }

    /**
     * Maneja la relación access_menu.
     */
    private function handleAccessMenu($menu, array $accessGroupIds, Request $request): bool
    {
        $accessMenu = [];
        foreach ($accessGroupIds as $accessGroupId) {
            $accessMenu[] = [
                'menu_id' => $menu->id,
                'access_group_id' => $accessGroupId,
                'access' => $request->input('access_crud_' . $accessGroupId),
            ];
        }

        return $menu->access_menu()->createMany($accessMenu) ? true : false;
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
            Log::error('Error fn(MenuController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja la edición o eliminación de un método de pago.
     *
     * @return \Illuminate\View\View|JsonResponse
     */
    private function handleViewAction(string $action, ?string $id = null)
    {
        try {
            $data = [];

            if ($action !== 'create') {
                $data['data'] = $this->model::findOrFail($id);
            }

            if ($action === 'edit' || $action === 'create') {
                $data['model'] = $this->help::listFile(app_path('/Models'), ['php']);
                $data['access_group'] = AccessGroup::pluck('name', 'id');
            }

            return view($this->view . '.' . $action, $data);
        } catch (\Exception $e) {
            Log::error('Error fn(MenuController) handleViewAction', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_DISPLAYING_MODEL')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    public function sorted(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->loopUpdateMenu(json_decode($request->input('sort')));
        }

        return response()->json(['status' => true, 'message' => trans('El menú se ha ordenado correctamente.')]);
    }

    private function loopUpdateMenu($menu, $parentMenu = null)
    {
        if ($menu) {
            foreach ($menu as $key => $dt) {
                if (
                    $this->model::find($dt->id)->update(['parent_id' => $parentMenu, 'sort' => $key + 1])
                    && isset($dt->children)
                    && count($dt->children) > 0
                ) {
                    $this->loopUpdateMenu($dt->children, $dt->id);
                }
            }
        }
    }

    public function listMenu(Request $request)
    {
        $menu = $this->model::with(['accessChildren'])->whereHas('access_menu', function ($query) use ($request) {
            $query->where('access_group_id', $request->user()->access_group_id);
        })->whereNull('parent_id')->show()->sort()->get();

        return response()->json(['menu' => $menu])->header('Content-Type', 'application/json');
    }
}
