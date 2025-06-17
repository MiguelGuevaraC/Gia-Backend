<?php
namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\CodeAsset;
use Illuminate\Support\Facades\Log; // Asegúrate de importar Log

class CodeGeneratorService
{

    public function generar(string $type = 'barcode', array $data = []): array
    {
        try {
            if (!in_array($type, ['barcode', 'qrcode', 'both'])) {
                throw new \InvalidArgumentException('Tipo inválido. Usa "barcode", "qrcode" o "both".');
            }

            // Generar código único de 8 caracteres
            do {
                $code = Str::upper(Str::random(8));
            } while (CodeAsset::where('code', $code)->exists());

            $encrypted = Crypt::encryptString($code);

            $barcodePath = null;
            $qrcodePath = null;

            if ($type === 'barcode' || $type === 'both') {
                $barcodeGen = new BarcodeGeneratorPNG();
                $barcodeImg = $barcodeGen->getBarcode($code, $barcodeGen::TYPE_CODE_128);
                $barcodePath = "barcodes/{$code}.png";
                Storage::disk('public')->put($barcodePath, $barcodeImg);
            }

            if ($type === 'qrcode' || $type === 'both') {
                $qrImg = QrCode::format('png')->generate($encrypted);
                $qrcodePath = "qrcodes/{$code}.png";
                Storage::disk('public')->put($qrcodePath, $qrImg);
            }

            // Combinar datos generados con los datos adicionales proporcionados
            $assetData = array_merge($data, [
                'code' => $code,
                'encrypted' => $encrypted,
                'barcode_path' => $barcodePath,
                'qrcode_path' => $qrcodePath,
            ]);

            CodeAsset::create($assetData);

            return [
                'code' => $code,
                'encrypted' => $encrypted,
                'barcode_url' => $barcodePath ? Storage::url($barcodePath) : null,
                'qrcode_url' => $qrcodePath ? Storage::url($qrcodePath) : null,
            ];
        } catch (\Throwable $e) {
            Log::error('Error al generar código: ' . $e->getMessage(), [
                'exception' => $e,
                'type' => $type,
                'data' => $data,
            ]);

            return []; // o puedes retornar null, o una estructura que indique el error de forma controlada
        }
    }


}
