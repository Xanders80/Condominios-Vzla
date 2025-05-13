<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\PaymentsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $paymentsService;

    public function __construct(PaymentsService $paymentsService)
    {
        $this->paymentsService = $paymentsService;
    }

    /**
     * Muestra la vista del índice del dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = $this->paymentsService->getDataCards(null, $this->paymentsService->getDwellerID());
        $years = $this->paymentsService->getYears();

        return view('backend.dashboard.index', compact('data', 'years'));
    }

    /**
     * Muestra la política de privacidad.
     *
     * @return \Illuminate\View\View
     */
    public function privacypolicy(Request $request)
    {
        return view('privacy-policy');
    }

    /**
     * Muestra los términos de uso.
     *
     * @return \Illuminate\View\View
     */
    public function termsofuse(Request $request)
    {
        return view('terms-of-use');
    }

    /**
     * Devuelve los datos de pago para un mes y año específicos en formato JSON.
     *
     * @param int|null $year el año para el que se solicitan los datos de pago
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentDataMonthByYear($year = null)
    {
        $data = $this->paymentsService->getPaymentDataMonthByYear($year ?? date('Y'), $this->paymentsService->getDwellerID());

        return response()->json($data);
    }

    /**
     * Devuelve los datos de pago para el año actual en formato JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentDataByYear()
    {
        $data = $this->paymentsService->getPaymentDataByYear($this->paymentsService->getDwellerID());

        return response()->json($data);
    }

    /**
     * Devuelve los datos de las tarjetas del dashboard para un año específico en formato JSON.
     *
     * @param int|null $year el año para el que se solicitan los datos de las tarjetas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dataCards($year = null)
    {
        $data = $this->paymentsService->getDataCards($year, $this->paymentsService->getDwellerID());

        return response()->json($data);
    }
}
