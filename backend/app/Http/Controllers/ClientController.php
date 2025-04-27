<?php

namespace App\Http\Controllers;

use App\Models\Client; 
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    
    public function index(): JsonResponse
    {
        try {
            $clients = Client::all(); 
            return response()->json($clients); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los clientes.', 'error' => $e->getMessage()], 500);
        }
    }
}
