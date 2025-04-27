<?php

namespace App\Http\Controllers;

use App\Services\CreditService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CreditController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /**
     * Store a newly created credit application in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $creditApplication = $this->creditService->createCreditApplication($data);
            return response()->json(['data' => $creditApplication, 'message' => 'Solicitud de crédito creada exitosamente'], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al crear la solicitud de crédito: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified credit application.
     */
    public function show(string $id)
    {
        try {
            $creditApplication = $this->creditService->getCreditApplication($id);
            return response()->json(['data' => $creditApplication], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Solicitud de crédito no encontrada'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al obtener la solicitud de crédito: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display a listing of the instalments for the specified credit application.
     */
    public function indexInstallments(string $id)
    {
        try {
            $instalments = $this->creditService->getInstallments($id);
            return response()->json(['data' => $instalments], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Solicitud de crédito no encontrada'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al obtener las cuotas: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Approve the specified credit application.
     */
    public function approve(string $id)
    {
        try {
            $creditApplication = $this->creditService->approveCreditApplication($id);
            return response()->json(['data' => $creditApplication, 'message' => 'Solicitud de crédito aprobada exitosamente'], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Solicitud de crédito no encontrada'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al aprobar la solicitud de crédito: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
