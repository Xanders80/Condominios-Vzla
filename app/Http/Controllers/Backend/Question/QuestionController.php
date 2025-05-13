<?php

namespace App\Http\Controllers\Backend\Question;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class QuestionController extends Controller
{
    /**
     * Muestra una lista paginada de preguntas frecuentes (FAQs).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = Faq::orderBy('visitors', 'desc')->paginate(10);

        return view($this->view . '.index', ['data' => $data]);
    }

    /**
     * Recupera preguntas frecuentes basadas en un código de página específico.
     *
     * @param string $page código de la página
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function page($page)
    {
        $faq = Faq::whereHas('menu', fn($query) => $query->where('code', $page))->select('faqs.title', 'faqs.id')->paginate(10);
        if (!$faq->isNotEmpty()) {
            $faq = Faq::orderBy('visitors', 'desc')->select('faqs.title', 'faqs.id')->paginate(10);
        }

        return response()->json($faq);
    }

    /**
     * Muestra los detalles de una pregunta frecuente específica.
     *
     * @param int $id ID de la FAQ
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        if ($data = Faq::find($id)) {
            $data->increment('visitors');
            $response = $data;
        }

        return view($this->view . '.show', ['data' => $response ?? []]);
    }

    /**
     * Prepara datos para DataTables.
     *
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function data()
    {
        $data = Faq::with('menu')->latest('visitors');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<a href="' . url(config('master.app.url.backend') . '/question/' . $row->id) . '" class="edit btn btn-info btn-xs pull-up"><i class="mdi mdi-eye mdi-18px text-primary"></i></a>';
            })
            ->rawColumns(['action'])
            ->make();
    }

    /**
     * Procesa las respuestas de los usuarios a las preguntas frecuentes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(Request $request)
    {
        $data = Faq::find($request->id);
        if (!$data) {
            return response()->json(['status' => false, 'message' => 'Ocurrió un error, por favor intente de nuevo'], 200);
        }

        $log = $data->log()->where('ip', $request->ip())->first();
        if ($log) {
            $this->updateLogAndCounters($data, $log, $request->code);
        } else {
            $this->createNewLogAndUpdateCounters($data, $request);
        }

        return $this->help::jsonResponse(true, 'Gracias por su respuesta.', 200);
    }

    /**
     * Actualiza el registro y los contadores basados en la interacción del usuario.
     *
     * @param Faq    $data
     * @param string $code
     *
     * @return void
     */
    private function updateLogAndCounters($data, $log, $code)
    {
        $currentCode = $log->data['code'];
        $newCode = $code;

        if ($currentCode !== $newCode) {
            $data->decrement($currentCode === 'yes' ? 'like' : 'dislike');
            $data->increment($newCode === 'yes' ? 'like' : 'dislike');
        }

        $log->update(['data' => ['code' => $newCode]]);
    }

    /**
     * Crea un nuevo registro y actualiza los contadores.
     *
     * @param Faq     $data
     * @param Request $request
     *
     * @return void
     */
    private function createNewLogAndUpdateCounters($data, $request)
    {
        $counterField = $request->code === 'yes' ? 'like' : 'dislike';
        $data->increment($counterField);

        $data->log()->create([
            'ip' => $request->ip(),
            'data' => ['code' => $request->code],
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Actualiza el conteo de visitantes basado en solicitudes AJAX.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateViewer(Request $request)
    {
        if ($request->ajax()) {
            if ($data = Faq::find($request->id)) {
                $data->increment('visitors');
                $response = $data;
            }

            return response()->json($response ?? ['status' => false, 'message' => 'Ocurrió un error, por favor intente de nuevo'], 200);
        }
        abort(404);
    }
}
