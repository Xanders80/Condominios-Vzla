<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\PostalCodeAddress;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ModelNotFoundException;
use RuntimeException;

class Helper
{
    /**
     * Obtiene el menú correspondiente al código proporcionado.
     *
     * @param string|null $code El código del menú a buscar. Si es nulo, se utiliza
     *                          el nombre de la ruta actual.
     * @return object|null retorna el objeto de menú encontrado o null si no se encuentra
     */
    public static function menu($code = null): ?object
    {
        try {
            $routeName = $code ?? Route::currentRouteName();
            $menuCode = explode('.', $routeName)[0];

            return Menu::where('code', $menuCode)
                ->where('active', true)
                ->first();
        } catch (\Exception $e) {
            Log::error("Error al obtener el menú: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Lista los archivos en un directorio dado que coinciden con las extensiones especificadas.
     *
     * @param string $path      ruta del directorio donde se buscarán los archivos
     * @param array  $extension array de extensiones de archivo que se mostrarán
     * @param bool   $recursive buscar recursivamente en subdirectorios
     * @return array array de nombres de archivos sin extensión
     */
    public static function listFile(
        string $path,
        array $extensions,
        bool $recursive = false
    ): array {
        if (!is_dir($path)) {
            throw new InvalidArgumentException("El directorio '$path' no existe");
        }

        if (empty($extensions)) {
            throw new InvalidArgumentException("Las extensiones no pueden estar vacías");
        }

        $files = $recursive
            ? File::allFiles($path)
            : File::files($path);

        return collect($files)
            ->filter(function ($file) use ($extensions) {
                return in_array($file->getExtension(), $extensions);
            })
            ->map(function ($file) {
                return pathinfo($file->getFilename(), PATHINFO_FILENAME);
            })
            ->all();
    }

    /**
     * Convertidor de bytes para transformar el tamaño en bytes a una unidad mayor.
     *
     * @param int $bytes cantidad de bytes a convertir
     * @return string representación formateada del tamaño en la unidad correspondiente
     */
    public static function bytesConverter(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = 1024;
        $unitIndex = 0;

        while ($bytes >= $factor && $unitIndex < count($units) - 1) {
            $bytes /= $factor;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Ordena una cadena de caracteres.
     *
     * @param string $text   texto que se va a ordenar
     * @param int    $length longitud máxima del texto
     * @return string texto ordenado
     */
    public static function sortText(string $text, int $length = 100): string
    {
        return Str::limit($text, $length, '...');
    }

    /**
     * Devuelve un arreglo con los años anterior, actual y posterior al año actual.
     *
     * @return array un arreglo con los años anterior, actual y posterior al año actual
     */
    public static function yearBeforeAfter(): array
    {
        $currentYear = Carbon::now()->year;
        return [
            $currentYear - 1 => (string) ($currentYear - 1),
            $currentYear => (string) $currentYear,
            $currentYear + 1 => (string) ($currentYear + 1)
        ];
    }

    /**
     * Convierte el formato de una moneda a un número normal sin puntos ni comas.
     *
     * @param string $number    cantidad de dinero a convertir
     * @param string $currency código de la moneda (por defecto 'Bs')
     * @return string número sin formato
     */
    public static function clearCurrencyFormat(string $number, string $currency = 'Bs'): string
    {
        return str_replace([$currency, '.', ','], '', $number);
    }

    /**
     * Cambia el formato de un número a moneda.
     *
     * @param float  $number    número a formatear
     * @param string $currency código de la moneda (por defecto 'Bs')
     * @return string número formateado como moneda
     */
    public static function currency(float $number, string $currency = 'Bs'): string
    {
        return $currency . ' ' . number_format($number, 0, ',', '.');
    }

    /**
     * Para cambiar el formato de un número añadiendo ceros al principio.
     *
     * @param int    $number número a formatear
     * @param int    $length longitud mínima del número
     * @return string número con ceros a la izquierda
     */
    public static function formatNumberWithZero(int $number, int $length = 5): string
    {
        return str_pad((string) $number, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Enviar notificación al usuario.
     *
     * @param mixed  $model   modelo que envía la notificación
     * @param mixed  $users   usuario(s) que recibirán la notificación
     * @param array  $array   datos de la notificación
     * @return void
     */
    public static function sendNotification($model, $users, array $array)
    {
        $users = is_array($users) ? $users : [$users];

        $model->notification()
            ->createMany(collect($users)->map(function ($item) use ($array) {
                return [
                    'user_id' => $item,
                    'data' => $array,
                    'status' => 0
                ];
            })->toArray());
    }

    /**
     * Este método se utiliza para filtrar cadenas que son iguales.
     *
     * @param string $text1 texto que se va a filtrar
     * @param string $text2 texto de comparación
     * @return string texto con las palabras únicas
     */
    public static function diffString(string $text1, string $text2): string
    {
        $string1 = explode(' ', $text1);
        $string2 = explode(' ', $text2);
        return implode(' ', array_diff($string1, $string2)) . ' ' . $text2;
    }

    /**
     * Generador de caracteres aleatorios.
     *
     * @return string palabra aleatoria del archivo words.json
     */
    public static function randomWord(): string
    {
        $words = json_decode(
            File::get(config_path('seeders/words.json')),
            true
        )['words'];

        return $words[array_rand($words)];
    }

    /**
     * Helper para subir imágenes en base64 al almacenamiento desde un editor de texto.
     *
     * @param string $content contenido que se convertirá en imagen
     * @param object $model   modelo sujeto al que se subirá la imagen
     * @return false|string
     */
    public static function uploadImageBase64(string $content, object $model)
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $image) {
            $base64Data = $image->getAttribute('src');
            $imgParts = explode(',', $base64Data, 2);

            if (count($imgParts) === 2 && strpos($imgParts[0], 'base64') !== false) {
                $imageData = base64_decode($imgParts[1]);
                $imageName = 'generated_image_' . time() . '_' . rand(1000, 9999) . '.webp';
                $imagePath = $model->folder . '/' . $imageName;

                if (Storage::disk(config('filesystems.default'))->put($imagePath, $imageData)) {
                    $file = $model->file()->create([
                        'data' => [
                            'disk' => config('filesystems.default'),
                            'target' => $imagePath,
                        ],
                        'alias' => 'image_editor',
                    ]);

                    $image->setAttribute('src', $file->link_stream);
                    $image->setAttribute('alt', $file->name);
                    $image->setAttribute('id', $file->id);
                }
            }
        }

        return $dom->saveHTML();
    }

    /**
     * Genera los botones de acción según los permisos del usuario y los botones requeridos.
     *
     * @param string $id           ID del registro
     * @param object $user         usuario actual
     * @param string $url         URL base para los botones
     * @param array  $requiredButtons Array de botones requeridos
     * @return string HTML de los botones de acción
     */
    public static function generateActionButtons(
        string $id,
        object $user,
        string $url,
        array $requiredButtons = ['show', 'edit', 'delete']
    ): string {
        $buttons = '';

        if (in_array('show', $requiredButtons) && $user->read) {
            $buttons .= self::actionButton('show', $id, 'Detail', 'mdi mdi-eye mdi-18px text-info', $url);
        }

        if (in_array('edit', $requiredButtons) && $user->update) {
            $buttons .= self::actionButton('edit', $id, 'Edit', 'mdi mdi-pencil-box-outline mdi-18px text-warning', $url);
        }

        if (in_array('delete', $requiredButtons) && $user->delete) {
            $buttons .= self::actionButton('delete', $id, 'Delete', 'mdi mdi-delete mdi-18px text-danger', $url);
        }

        return $buttons;
    }

    /**
     * Genera un botón de acción.
     *
     * @param string $action el tipo de acción (show, edit, delete)
     * @param string $id     el ID del registro
     * @param string $title  el título del botón
     * @param string $icon   el icono a mostrar en el botón
     * @param string $url    URL base para el botón
     * @return string el HTML del botón de acción
     */
    public static function actionButton(
        string $action,
        string $id,
        string $title,
        string $icon,
        string $url
    ): string {
        return '<x-button-button class="btn-action btn btn-sm btn-outline pull-up"
            data-title="' . trans($title) . '"
            data-action="' . $action . '"
            data-url="' . $url . '"
            data-id="' . $id . '"
            title="' . trans($title) . '">
            <span class="' . $icon . '"></span>
        </x-button-button>';
    }

    /**
     * Obtiene la lista de meses del año.
     *
     * @return array array con los meses del año traducidos
     */
    public static function getMonths(): array
    {
        return Cache::remember('months_list', now()->addHours(24), function () {
            return [
                1 => trans('January'),
                2 => trans('February'),
                3 => trans('March'),
                4 => trans('April'),
                5 => trans('May'),
                6 => trans('June'),
                7 => trans('July'),
                8 => trans('August'),
                9 => trans('September'),
                10 => trans('October'),
                11 => trans('November'),
                12 => trans('December'),
            ];
        });
    }

    /**
     * Get address by zone based on an ID.
     *
     * @param int $id ID de la zona postal
     * @return string dirección formateada
     */
    public static function getAddressById(int $id): string
    {
        try {
            $postalCode = PostalCodeAddress::where('postal_zone.id', $id)
                ->selectRaw("CONCAT(
                    postal_zone.name, ', Parroquia ',
                    parishes.name, ', Ciudad ',
                    MAX(cities.name), ', Municipio ',
                    municipalities.name, ', Estado ',
                    states.name, ', VE, Código Postal: ',
                    postal_zone.zip_code
                ) AS data")
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
                ->first();

            if (!$postalCode) {
                throw new ModelNotFoundException(
                    "No se encontró la dirección para el ID: $id"
                );
            }

            return $postalCode->data;
        } catch (ModelNotFoundException $e) {
            Log::warning($e->getMessage());
            return 'No se encontró la dirección';
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new RuntimeException(
                "Error al obtener la dirección: {$e->getMessage()}"
            );
        }
    }

    /**
     * Retorna una respuesta JSON estandarizada.
     *
     * @param bool   $status    estado de la respuesta
     * @param string $message   mensaje de respuesta
     * @param int    $httpStatus código HTTP de respuesta
     * @param array  $errors    array de errores (opcional)
     * @param array  $data      datos de respuesta (opcional)
     * @return \Illuminate\Http\JsonResponse
     */
    public static function jsonResponse(
        bool $status,
        string $message,
        int $httpStatus,
        array $errors = [],
        array $data = []
    ): \Illuminate\Http\JsonResponse {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $httpStatus);
    }
}
