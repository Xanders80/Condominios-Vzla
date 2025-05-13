<?php

namespace App\Http\Controllers\Backend\AccessMenu;

use App\Http\Controllers\Controller;
use App\Models\AccessGroup;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessMenuController extends Controller
{
    /**
     * Muestra la vista principal del menú de acceso.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->view . '.index');
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
     * Muestra la vista para editar un grupo de acceso específico.
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
     * Actualiza los menús asociados a un grupo de acceso.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        return $this->handleUpdate($request, $id);
    }

    /**
     * Muestra la vista de eliminación de un grupo de acceso.
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
            $data = AccessGroup::all();

            return datatables()->of($data)
                ->addColumn('action', fn($data) => "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id,$request->user(),$this->url)}</div>")
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fn(AccessMenuController) handleViewAction', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_DISPLAYING_MODEL')),
                'error' => $e->getMessage(),
                'data' => 'Data: ' . $data,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    /**
     * Elimina un grupo de acceso.
     *
     * @param int $access_group_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($access_group_id)
    {
        try {
            $response = [];

            // Utiliza la función deleteData del modelo
            $response = $this->model::deleteData($access_group_id);
        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = trans(config('constants.MESSAGES.DATA_DELETE_FAILED')) . trans(config('constants.MESSAGES.ERROR_TRYING_TO_DELETE_RESOURCE')) . ': ' . " $access_group_id: " . $e->getMessage();
            $response['status_code'] = config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR');
            Log::error('Error fn(AccessMenuController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $access_group_id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja la visualización de un grupo de acceso específico.
     *
     * @param int    $id
     * @param string $action 'show' o 'edit'
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    private function handleViewAction(string $action, ?string $id = null)
    {
        try {
            $data = AccessGroup::findOrFail($id);

            if ($action === 'edit' || $action === 'show') {
                $data['menu'] = Menu::all();
            }

            return view($this->view . '.' . $action, compact('data'));
        } catch (\Exception $e) {
            Log::error('Error fn(AccessMenuController) handleViewAction', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_DISPLAYING_MODEL')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    /**
     * Maneja la actualización de los menús asociados a un grupo de acceso.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleUpdate(Request $request, int $id)
    {
        $status = false;
        $message = trans(config('constants.MESSAGES.MESS_BAD_REQUEST'));
        $httpStatus = config('constants.STATUS_CODES.NOT_FOUND');

        try {
            // Validar los datos utilizando el método validate del modelo AccessGroup
            $validation = AccessGroup::validationRules($request->all(), $id);

            if ($validation->fails()) {
                return $this->help::jsonResponse($status, $message, $httpStatus, $validation->errors()->toArray());
            }

            // Crear una colección de menús asociados al grupo de acceso
            $data = collect($request->menu_id)->map(fn($item) => ['access_group_id' => $id, 'menu_id' => $item]);

            // Eliminar menús asociados al grupo de acceso anteriormente
            $this->model::whereAccessGroupId($id)->forceDelete();

            // Crear nuevos menús asociados al grupo de acceso
            if (AccessGroup::find($id)->access_menu()->createMany($data->toArray())) {
                $status = AccessGroup::find($id)->access_menu()->createMany($data->toArray()) ? true : false;
            }

            $message = $status ? trans(config('constants.MESSAGES.DATA_SUCCESS')) : trans(config('constants.MESSAGES.DATA_FAILED'));
            $httpStatus = $status ? config('constants.STATUS_CODES.OK') : config('constants.STATUS_CODES.NOT_FOUND');
        } catch (\Exception $e) {
            $errorMessage = trans(config('constants.MESSAGES.DATA_FAILED')) . trans('Error trying handleUpdate with ID') . ' ' . $id . ' Access Menu: ' . $e->getMessage();
            Log::error($errorMessage);

            return $this->help::jsonResponse(false, $errorMessage, config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }

        return $this->help::jsonResponse($status, $message, $httpStatus);
    }
}
