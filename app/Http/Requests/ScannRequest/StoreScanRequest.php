<?php

namespace App\Http\Requests\ScannRequest;

use App\Http\Requests\StoreRequest;
use App\Models\CodeAsset;
use App\Models\ScanLog;
use App\Services\CodeGeneratorService;

class StoreScanRequest extends StoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'encrypted' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    try {
                        // 1. Desencriptar usando Hashids personalizado
                        $codeNumeric = CodeGeneratorService::decryptShort($value);

                        if (!$codeNumeric) {
                            return $fail('Código inválido o corrupto.');
                        }

                        // 2. Buscar el CodeAsset con ese número como código
                        $codeAsset = CodeAsset::where('code', (string) $codeNumeric)->first();

                        if (!$codeAsset) {
                            return $fail('Código no válido o no registrado.');
                        }

                        // 3. Verificar si ya fue escaneado con éxito
                        $alreadyScanned = ScanLog::where('code_asset_id', $codeAsset->id)
                            ->where('status', 'ok')
                            ->exists();

                        if ($alreadyScanned) {
                            ScanLog::create([
                                'ip' => $this->ip(),
                                'code_asset_id' => $codeAsset->id,
                                'status' => 'denied',
                                'description' => 'Este código ya fue escaneado.',
                                 "code" => $value,
                            ]);

                            return $fail('Este código ya fue escaneado.');
                        }

                        // 4. Guardar info para el controlador
                        $this->merge([
                            'code' => $codeAsset->code,
                            'code_asset_id' => $codeAsset->id
                        ]);
                    } catch (\Throwable $e) {
                        return $fail('Error al procesar el código.');
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'encrypted.required' => 'El campo "Código de barra" es obligatorio.',
            'encrypted.string' => 'El campo "Código de barra" debe ser una cadena de texto.',
        ];
    }
}
