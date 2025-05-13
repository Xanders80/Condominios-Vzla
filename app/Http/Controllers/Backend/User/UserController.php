<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Models\AccessGroup;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Muestra la vista del índice de usuarios.
     */
    public function index(): View
    {
        return $this->handleViewAction('index');
    }

    /**
     * Muestra la vista para crear un nuevo usuario.
     *
     * @return View
     */
    public function create()
    {
        return $this->handleViewAction('create');
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     */
    public function store(Request $request): JsonResponse
    {
        return $this->handleStoreOrUpdate($request, 'store');
    }

    /**
     * Muestra la vista de edición de un usuario existente.
     *
     * @param int $id
     *
     * @return View
     */
    public function edit($id)
    {
        return $this->handleViewAction('edit', $id);
    }

    /**
     * Actualiza un usuario existente en la base de datos.
     *
     * @param int $id
     */
    public function update(Request $request, $id): JsonResponse
    {
        return $this->handleStoreOrUpdate($request, 'update', $id);
    }

    /**
     * Muestra la vista de eliminación de un usuario específico.
     *
     * @param int $id
     *
     * @return View
     */
    public function delete($id)
    {
        return $this->handleViewAction('delete', $id);
    }

    /**
     * Muestra los detalles de un usuario.
     */
    public function detail(string $id): object
    {
        try {
            $data = $this->findUserOrFail($id);

            return view('backend.user.detail', compact('data'));
        } catch (\Exception $e) {
            $errorMessage = trans(config('constants.MESSAGES.ERROR_TRYING_TO_DISPLAY')) . ' Error al mostrar detalle de usuario: ' . $e->getMessage();
            Log::error($errorMessage);

            return $this->help::jsonResponse(false, $errorMessage, config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
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
     * @return JsonResponse retorna una respuesta JSON con los datos formateados
     *                      para el datatable, incluyendo las columnas de índice, acción y estado de publicación
     */
    public function data(Request $request): object
    {
        try {
            $data = $this->model::filterLevel()->with('level', 'access_group');

            return datatables()->of($data)
                ->filterColumn('name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
                })
                ->editColumn('email_verified_at', fn($data) => $this->getVerifiedIcon($data->email_verified_at))
                ->addColumn('action', fn($data) => "<div class='btn-group pull-up'>{$this->help::generateActionButtons($data->id,$request->user(),$this->url, ['edit', 'delete'])}</div>")
                ->addIndexColumn()
                ->rawColumns(['action', 'email_verified_at'])
                ->make();
        } catch (\Exception $e) {
            Log::error('Error fn(UserController) handleViewAction', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')) . trans(config('constants.MESSAGES.ERROR_DISPLAYING_MODEL')),
                'error' => $e->getMessage(),
                'data' => 'Data: ' . $data,
            ]);

            return $this->help::jsonResponse(false, trans(config('constants.MESSAGES.DATA_ERROR')), config('constants.STATUS_CODES.INTERNAL_SERVER_ERROR'));
        }
    }

    /**
     * Elimina un usuario específico de la base de datos.
     *
     * @param int $id
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
            Log::error('Error fn(UserController) destroy', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'ID: ' . $id,
            ]);
        }

        // Retornar respuesta final
        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    /**
     * Busca un usuario por ID y lanza una excepción si no se encuentra.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    private function findUserOrFail(string $id): User
    {
        return $this->model::findOrFail($id);
    }

    /**
     * Maneja la visualización de un formulario de edición o eliminación de un documento.
     *
     * @param int $id
     *
     * @return View|JsonResponse
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
            Log::error('Error fn(UserController) handleViewAction', [
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
                $viewData['level'] = Level::filterLevel()->pluck('name', 'id');
                $viewData['access_group'] = AccessGroup::filterLevel()->pluck('name', 'id');
                break;
            case 'delete':
                break;
            case 'index':
                $userCounts = (new User())->calculateUserCounts();

                $viewData['data'] = [
                    [
                        'label' => trans('Users'),
                        'message' => $userCounts['totalUsers'],
                        'sub_message' => '100', // Puede que sea dinámico en el futuro.
                        'end_text' => trans('Total Users'),
                        'icon' => 'mdi mdi-account-multiple mdi-36px',
                    ],
                    [
                        'label' => trans('Verified Users'),
                        'message' => $userCounts['verifiedUsers'],
                        'sub_message' => "{$userCounts['verifiedPercentage']}",
                        'end_text' => trans('Recent analytics'),
                        'icon' => 'mdi mdi-account-check mdi-36px',
                    ],
                    [
                        'label' => trans('Verification Pending'),
                        'message' => $userCounts['pendingVerification'],
                        'sub_message' => "{$userCounts['pendingPercentage']}",
                        'end_text' => trans('Recent analytics'),
                        'icon' => 'mdi mdi-account-alert mdi-36px mdi-dark mdi-inactive',
                    ],
                ];
                break;
            default:
                throw new \InvalidArgumentException("Invalid action: $action");
        }

        return $viewData;
    }

    /**
     * Maneja el almacenamiento o actualización de un usuario.
     *
     * @return JsonResponse
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
            Log::error('Error fn(UserController) handleStoreUpdate', [
                'detail' => trans(config('constants.MESSAGES.DATA_ERROR')),
                'error' => $e->getMessage(),
                'data' => 'Action: ' . $action . ', ID: ' . $id,
            ]);
        }

        return $this->help::jsonResponse($response['status'], $response['message'], $response['status_code']);
    }

    private function getVerifiedIcon($isVerified)
    {
        return $isVerified
            ? config('constants.ICONS.VERIFIED')
            : config('constants.ICONS.UNVERIFIED');
    }
}
