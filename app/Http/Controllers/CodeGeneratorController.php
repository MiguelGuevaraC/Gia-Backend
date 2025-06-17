<?php

namespace App\Http\Controllers;

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
}
