<?php
namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\CodeAsset;
use App\Models\Entry;
use App\Models\LotteryTicket;
use App\Models\Reservation;
use App\Models\ScanLog;
use Illuminate\Support\Facades\Log; // Asegúrate de importar Log
use Vinkla\Hashids\Facades\Hashids;
class CodeGeneratorService
{

    private function encryptShort(string $code): string
    {
        // Hashear el code original como string → Hashids trabaja con enteros, así que transformamos el code a un número único y corto
        $numeric = crc32($code); // Más corto que hexdec(md5())
        return Hashids::encode($numeric); // Hash único pero corto
    }

    public static function decryptShort(string $hash): ?string
    {
        $decoded = Hashids::decode($hash);

        if (empty($decoded)) {
            return null;
        }

        $numeric = $decoded[0];

        // Buscar en la base de datos el `code` que tenga ese hash generado
        $codeAsset = CodeAsset::whereRaw('CRC32(code) = ?', [$numeric])->first();

        return $codeAsset?->code;
    }



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

            $encrypted = $this->encryptShort($code); // más corto


            $barcodePath = null;
            $qrcodePath = null;

            $folder = 'otros';
            if (!empty($data['entry_id'])) {
                $folder = 'entradas';
            } elseif (!empty($data['reservation_id'])) {
                $folder = 'reservas';
            } elseif (!empty($data['lottery_ticket_id'])) {
                $folder = 'tickets';
            }

            if ($type === 'barcode' || $type === 'both') {
                $barcodeGen = new BarcodeGeneratorPNG();
                $barcodeImg = $barcodeGen->getBarcode($encrypted, $barcodeGen::TYPE_CODE_128);
                $barcodePath = "{$folder}/barcodes/{$encrypted}.png";
                Storage::disk('public')->put($barcodePath, $barcodeImg);
            }

            if ($type === 'qrcode' || $type === 'both') {
                $qrImg = QrCode::format('png')->generate($encrypted);
                $qrcodePath = "{$folder}/qrcodes/{$encrypted}.png";
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


    public function registrarEscaneo(string $encrypted, string $ip): array
    {
        try {
            $code = $this->decryptShort($encrypted);

            $codeAsset = CodeAsset::where('code', $code)->first();

            if (!$codeAsset) {
                ScanLog::create([
                    'code_asset_id' => null,
                    'ip' => $ip,
                    'status' => 'denied',
                    'description' => 'Código no registrado.',
                     "code" => $encrypted,
                ]);

                return [
                    'status' => 'denied',
                    'message' => 'Código no válido',
                ];
            }

            // ✅ Verificar a qué modelo está relacionado y actualizar el estado
            if ($codeAsset->entry_id) {
                $entry = Entry::find($codeAsset->entry_id);
                if ($entry) {
                    $entry->status_entry = 'Código Escaneado';
                    $entry->save();
                }
            } elseif ($codeAsset->reservation_id) {
                $reservation = Reservation::find($codeAsset->reservation_id);
                if ($reservation) {
                    $reservation->status_scan = 'Código Escaneado';
                    $reservation->save();
                }
            } elseif ($codeAsset->lottery_ticket_id) {
                $ticket = LotteryTicket::find($codeAsset->lottery_ticket_id);
                if ($ticket) {
                    $ticket->status_scan = 'Código Escaneado';
                    $ticket->save();
                }
            }

            // Registrar escaneo exitoso
            ScanLog::create([
                'code_asset_id' => $codeAsset->id,
                'ip' => $ip,
                'status' => 'ok',
                'description' => 'Escaneo permitido.',
                "code" => $encrypted,
            ]);

            return [
                'status' => 'ok',
                'message' => 'Escaneo registrado correctamente',
            ];
        } catch (\Throwable $e) {
            ScanLog::create([
                'code_asset_id' => null,
                'ip' => $ip,
                'status' => 'denied',
                'description' => 'Error al desencriptar el código: ' . $e->getMessage(),
            ]);

            return [
                'status' => 'denied',
                'message' => 'Error en el escaneo',
            ];
        }
    }

}
