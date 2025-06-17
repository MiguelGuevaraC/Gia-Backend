<?php
namespace App\Services;

use Culqi\Culqi;
use Illuminate\Http\Request;

class CulquiService
{

    protected $culqi;

    public function __construct()
    {
        $this->culqi = new Culqi([
            'api_key' => config('services.culqi.secret_key'),
        ]);
    }

    /**
     * Crea un cargo con Culqi.
     *
     * @throws \Exception
     */
    public function createCharge(Request $request): array
    {
        $charge = $this->culqi->Charges->create([
            "amount" => $request->amount,
            "capture" => true,
            "currency_code" => "PEN",
            "description" => $request->description !== '-' ? $request->description : 'Pago de pedido',
            "email" => $request->email,
            "installments" => 0,
            "source_id" => $request->token,
        ]);

        if (!isset($charge->id)) {
            return [
                'success' => false,
                'message' => $charge->user_message ?? $charge->merchant_message ?? 'Error al procesar el pago',
                'object' => $charge,
                'status' => 400,
            ];
        }

        return [
            'success' => true,
            'message' => 'Pago procesado correctamente',
            'object' => $charge,
            'status' => 200,
        ];
    }

}
