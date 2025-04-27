<?php

namespace App\Http\Controllers;

use App\Models\Phone; 
use Illuminate\Http\JsonResponse;

class PhoneController extends Controller
{
    
    public function index(): JsonResponse
    {
        try {
            $phones = Phone::all(); 
            return response()->json($phones); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los modelos de celular.', 'error' => $e->getMessage()], 500);
        }
    }
}
