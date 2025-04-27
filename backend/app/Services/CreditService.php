<?php

namespace App\Services;

use App\Models\Client;
use App\Models\CreditApplication;
use App\Models\Installment;
use App\Models\Phone;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreditService
{
    private const MONTHLY_INTEREST_RATE = 0.015; // 1.5%

    /**
     * Crea una nueva solicitud de crédito.
     *
     * @param array $data Datos de la solicitud de crédito (client_id, phone_id, term).
     * @return \App\Models\CreditApplication La solicitud de crédito creada.
     * @throws \InvalidArgumentException Si hay errores de validación o lógica de negocio.
     */
    public function createCreditApplication(array $data): CreditApplication
    {
        $clientId = $data['client_id'];
        $phoneId = $data['phone_id'];
        $term = $data['term'];

        // Iniciar una transacción de base de datos para asegurar la integridad
        return DB::transaction(function () use ($clientId, $phoneId, $term) {
            // 1. Validar la elegibilidad crediticia del cliente
            $hasActiveCredit = CreditApplication::where('client_id', $clientId)
                ->whereIn('state', ['approved', 'pending'])
                ->exists();

            if ($hasActiveCredit) {
                throw new InvalidArgumentException('El cliente ya tiene una solicitud de crédito activa.');
            }

            // 2. Verificar la disponibilidad del teléfono
            $phone = Phone::lockForUpdate()->findOrFail($phoneId); // Bloquear la fila para evitar problemas de concurrencia
            if ($phone->stock <= 0) {
                throw new InvalidArgumentException('El modelo de celular seleccionado no tiene stock disponible.');
            }

            // 3. Calcular el plan de pagos
            $totalAmount = $phone->price;
            $monthlyInterest = self::MONTHLY_INTEREST_RATE;
            $monthlyPayment = ($totalAmount * $monthlyInterest * pow(1 + $monthlyInterest, $term)) / (pow(1 + $monthlyInterest, $term) - 1);
            $monthlyPayment = round($monthlyPayment, 2);

            // 4. Registrar la solicitud de crédito
            $creditApplication = new CreditApplication();
            $creditApplication->client_id = $clientId;
            $creditApplication->phone_id = $phoneId;
            $creditApplication->amount = $totalAmount;
            $creditApplication->term = $term;
            $creditApplication->state = 'pending'; // Estado inicial
            $creditApplication->save();

            // Generar las cuotas (instalments)
            for ($i = 1; $i <= $term; $i++) {
                $dueDate = now()->addMonths($i)->toDateString();
                $installment = new Installment();
                $installment->credit_application_id = $creditApplication->id;
                $installment->installment_number = $i;
                $installment->amount = $monthlyPayment;
                $installment->due_date = $dueDate;
                $installment->save();
            }

            return $creditApplication;
        });
    }

    /**
     * Obtiene una solicitud de crédito por su ID.
     *
     * @param int $id ID de la solicitud de crédito.
     * @return \App\Models\CreditApplication|null La solicitud de crédito o null si no se encuentra.
     */
    public function getCreditApplication(int $id): ?CreditApplication
    {
        return CreditApplication::findOrFail($id);
    }

    /**
     * Obtiene las cuotas de una solicitud de crédito por su ID.
     *
     * @param int $creditApplicationId ID de la solicitud de crédito.
     * @return \Illuminate\Database\Eloquent\Collection Una colección de cuotas.
     */
    public function getInstallments(int $creditApplicationId)
    {
        $creditApplication = CreditApplication::findOrFail($creditApplicationId);
        return $creditApplication->instalments;
    }

    /** Aprobar Crédito */
    public function approveCreditApplication(int $creditApplicationId): CreditApplication
    {
        return DB::transaction(function () use ($creditApplicationId) {
            $creditApplication = CreditApplication::findOrFail($creditApplicationId);

            if ($creditApplication->state === 'approved') {
                throw new InvalidArgumentException('La solicitud de crédito ya está aprobada.');
            }

            if ($creditApplication->state !== 'pending') {
                throw new InvalidArgumentException('Solo se pueden aprobar solicitudes de crédito en estado pendiente.');
            }

            $phone = Phone::lockForUpdate()->findOrFail($creditApplication->phone_id);

            if ($phone->stock <= 0) {
                throw new InvalidArgumentException('No hay stock disponible para el teléfono asociado a esta solicitud.');
            }

            // Cambiar el estado de la solicitud a aprobado
            $creditApplication->state = 'approved';
            $creditApplication->save();

            // Descontar el stock del teléfono
            $phone->stock--;
            $phone->save();

            return $creditApplication;
        });
    }
}
