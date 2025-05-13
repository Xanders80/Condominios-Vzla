<?php

namespace App\Http\Controllers\Backend\File;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FacadesFile;

class FileController extends Controller
{
    /**
     * Obtiene un archivo específico y lo devuelve.
     *
     * @param int    $id       ID del archivo
     * @param string $filename nombre del archivo
     *
     * @return \Illuminate\Http\Response
     */
    public function getFile($id, $filename)
    {
        $file = File::find($id);

        if ($file && $file->exists) {
            return response()->make($file->take, 200, [
                'Content-Type' => $file->mime,
                'Content-Disposition' => 'inline; filename="' . $filename . '.' . $file->extension . '"',
            ]);
        }

        return view('errors.404', [
            'data' => [
                'code' => 410,
                'status' => 'GONE',
                'title' => 'Archivo No Encontrado',
                'message' => 'Lo siento, el archivo que buscas no se encuentra',
            ],
        ]);
    }

    /**
     * Permite la descarga de un archivo específico.
     *
     * @param int    $id       ID del archivo
     * @param string $filename nombre del archivo
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($id, $filename)
    {
        $file = File::find($id);

        if ($file && $file->exists) {
            return response()->make($file->take, 200, [
                'Content-Type' => $file->mime,
                'Content-Disposition' => 'attachment; filename="' . $filename . '.' . $file->extension . '"',
            ]);
        }

        return view('errors.404', [
            'data' => [
                'code' => 410,
                'status' => 'GONE',
                'title' => 'Archivo No Encontrado',
                'message' => 'Lo siento, el archivo que buscas no se encuentra',
            ],
        ]);
    }

    /**
     * Elimina un archivo específico.
     *
     * @param int    $id       ID del archivo
     * @param string $filename nombre del archivo
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ModelNotFoundException
     */
    public function deleteFile($id, $filename)
    {
        $file = File::find($id);

        if ($file && $file->exists()) {
            $file->delete();

            return response()->json(['status' => true, 'message' => "El archivo $filename ha sido eliminado"]);
        }

        throw new ModelNotFoundException("Archivo $filename no encontrado", 404);
    }

    /**
     * Transmite un archivo público basado en un código de menú.
     *
     * @param string $code_menu código del menú
     *
     * @return \Illuminate\Http\Response
     */
    public function publicFileStream(Request $request, $code_menu)
    {
        $code = $request->code;
        $response = null;

        try {
            $menu = $this->help->menu($code_menu);
            $data = $menu->model::find($request->id) ?? $menu->model::first();
            $file = $data->file()->whereAlias($code)->first();
            $path = public_path(config('master.app.web.template') . '/' . $file->target);

            if ($file->exists()) {
                $response = response()->make($file->take, 200, [
                    'Content-Type' => $file->mime,
                    'Content-Disposition' => 'inline; filename="' . $file->name . '.' . $file->extension . '"',
                ]);
            } elseif (FacadesFile::exists($path)) {
                $response = response()->file($path);
            } else {
                $response = file_get_contents($file->target);
            }
        } catch (\Throwable $th) {
            $response = view('errors.404', [
                'data' => [
                    'code' => 404,
                    'status' => 'GONE',
                    'file' => $code,
                    'title' => 'Archivo ' . $code . ' No Encontrado',
                    'message' => 'Lo siento, el archivo que buscas no se encuentra',
                ],
            ]);
        }

        return $response;
    }

    /**
     * Maneja la carga de imágenes desde un editor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleEditorImageUpload(Request $request)
    {
        $file = $request->file('file');
        $name = uniqid() . '_' . $file->getClientOriginalName();
        $mime = $file->getMimeType();
        $image = base64_encode(file_get_contents($file));

        return response()->json([
            'status' => true,
            'message' => 'La imagen se ha subido correctamente',
            'data' => [
                'name' => $name,
                'url' => 'data:' . $mime . ';base64,' . $image,
            ],
        ]);
    }
}
