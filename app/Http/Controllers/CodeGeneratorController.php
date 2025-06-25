<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScannRequest\StoreScanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\CodeGeneratorService;

class CodeGeneratorController extends Controller
{
    protected $generator;

    public function __construct(CodeGeneratorService $generator)
    {
        $this->generator = $generator;
    }

    public function generar(Request $request)
    {
        $type = $request->input('type_code', 'barcode');

        try {
            $result = $this->generator->generar($type);
            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function scanner(StoreScanRequest $request): JsonResponse
    {
        if ($request->header('UUID') !== 'ZXCV-CVBN-VBNM') {
            return response()->json(['status' => 'unauthorized'], 401);
        }
        
        $encrypted = $request->input('encrypted');
        $ip = $request->ip();

        $result = $this->generator->registrarEscaneo($encrypted, $ip);

        return response()->json($result, $result['status'] === 'ok' ? 200 : 400);
    }

}
