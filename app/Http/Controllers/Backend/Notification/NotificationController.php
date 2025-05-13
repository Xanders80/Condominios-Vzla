<?php

namespace App\Http\Controllers\Backend\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
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
     * Muestra el formulario de Detalles para un registro específico.
     *
     * @param int $id el ID del registro a mostrar
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->handleViewAction('show', $id);
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
            $data = $this->model::all();

            return datatables()->of($data)
                ->editColumn('status', function ($data) {
                    return $data->status
                        ? '<span class="badge badge-success">' . trans('Active') . '</span>'
                        : '<span class="badge badge-danger">' . trans('Inactive') . '</span>';
                })
                ->addColumn('action', fn($data) => "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id,$request->user(),$this->url, ['show', 'delete'])}</div>")
                ->addIndexColumn()
                ->rawColumns(['action', 'status'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error fn(NotificationController) handleViewAction', [
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
            Log::error('Error fn(NotificationController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Maneja la visualización de un formulario de edición o eliminación del Tipo de unidad.
     *
     * @param int    $id     el ID del Tipo de Unidad a editar o eliminar
     * @param string $action 'edit' o 'delete'
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    private function handleViewAction(string $action, ?string $id = null)
    {
        try {
            $data = $this->model::findOrFail($id);

            return view($this->view . '.' . $action, compact('data'));
        } catch (\Exception $e) {
            Log::error('Error fn(NotificationController) handleViewAction', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_DISPLAYING_MODEL')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    public function getNotification(Notification $notification)
    {
        return response()->json($notification->fetchNotification());
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        $notification->markAsRead($request->user()->id);

        return response()->json(['status' => true]);
    }

    public function getSideBarNotification()
    {
        try {
            // code menu
            $response['sidebar_notification'] = [
                'announcement' => 0,
                'user' => 0,
                'level' => 0,
            ];

            foreach ($response['sidebar_notification'] as $code => $total) {
                $response = $this->menuRecursive($response, $this->help->menu($code)->parent, $total);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json($response);
    }

    private function menuRecursive(mixed $response, $menu = null, $total = 0)
    {
        if (!is_null($menu)) {
            if (array_key_exists($menu->code, $response['sidebar_notification'])) {
                $response['sidebar_notification'][$menu->code] += $total;
            } else {
                $response['sidebar_notification'] += [$menu->code => $total];
            }
            $this->menuRecursive($response, $menu->parent, $total);
        }

        return $response;
    }
}
