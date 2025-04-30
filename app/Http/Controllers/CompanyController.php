<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest\IndexCompanyRequest;
use App\Http\Requests\CompanyRequest\StoreCompanyRequest;
use App\Http\Requests\CompanyRequest\UpdateCompanyRequest;
use App\Http\Resources\CompanyAppResource;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;

class CompanyController extends Controller
{

    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/company",
     *     summary="Obtener información con filtros y ordenamiento",
     *     tags={"Company"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="ruc", in="query", description="Filtrar por ruc de la empresa", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="business_name", in="query", description="Filtrar razón social", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="address", in="query", description="Filtrar por dirección", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="phone", in="query", description="Filtrar por telefono", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="query", description="Filtrar por email", required=false, @OA\Schema(type="string")),

     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de Empresas", @OA\JsonContent(ref="#/components/schemas/Company")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(type="object", @OA\Property(property="error", type="string")))
     * )
     */

    public function index(IndexCompanyRequest $request)
    {

        return $this->getFilteredResults(
            Company::class,
            $request,
            Company::filters,
            Company::sorts,
            CompanyResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/company-list",
     *     summary="Obtener información con filtros y ordenamiento",
     *     tags={"Company"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="ruc", in="query", description="Filtrar por ruc de la empresa", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="business_name", in="query", description="Filtrar razón social", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="address", in="query", description="Filtrar por dirección", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="phone", in="query", description="Filtrar por telefono", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="query", description="Filtrar por email", required=false, @OA\Schema(type="string")),

     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de Empresas", @OA\JsonContent(ref="#/components/schemas/Company")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(type="object", @OA\Property(property="error", type="string")))
     * )
     */

    public function list(IndexCompanyRequest $request)
    {

        return $this->getFilteredResults(
            Company::class,
            $request,
            Company::filters,
            Company::sorts,
            CompanyAppResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/company/{id}",
     *     summary="Obtener detalles de un company por ID",
     *     tags={"Company"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la empresa", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Company encontrada", @OA\JsonContent(ref="#/components/schemas/Company")),
     *     @OA\Response(response=404, description="Company No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Company No Encontrado")))
     * )
     */

    public function show($id)
    {

        $company = $this->companyService->getCompanyById($id);

        if (! $company) {
            return response()->json([
                'error' => 'Company No Encontrado',
            ], 404);
        }

        return new CompanyResource($company);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/company",
     *     summary="Crear company",
     *     tags={"Company"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "ruc", "business_name", "status"},
     *                 @OA\Property(property="name", type="string", example="Empresa X"),
     *                 @OA\Property(property="ruc", type="string", example="12345678901"),
     *                 @OA\Property(property="business_name", type="string", example="Razón Social S.A."),
     *                 @OA\Property(property="address", type="string", example="Calle Ficticia 123"),
     *                 @OA\Property(property="phone", type="string", example="987654321"),
     *                 @OA\Property(property="email", type="string", example="contacto@empresa.com"),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="route", type="string", description="Archivo de la imagen", format="binary"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Company creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Company")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un company asociado.")))
     * )
     */
    public function store(StoreCompanyRequest $request)
    {
        $company = $this->companyService->createCompany($request->validated());
        return new CompanyResource($company);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/company/{id}",
     *     summary="Actualizar Company",
     *     tags={"Company"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa que se desea actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={},
     *                 @OA\Property(property="name", type="string", example="Empresa X"),
     *                 @OA\Property(property="ruc", type="string", example="12345678901"),
     *                 @OA\Property(property="business_name", type="string", example="Razón Social S.A."),
     *                 @OA\Property(property="address", type="string", example="Calle Ficticia 123"),
     *                 @OA\Property(property="phone", type="string", example="987654321"),
     *                 @OA\Property(property="email", type="string", example="contacto@empresa.com"),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="route", type="string", description="Archivo de la imagen", format="binary"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Company actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Company")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un company asociado.")))
     * )
     */

    public function update(UpdateCompanyRequest $request, $id)
    {

        $validatedData = $request->validated();

        $company = $this->companyService->getCompanyById($id);
        if (! $company) {
            return response()->json([
                'error' => 'Company No Encontrado',
            ], 404);
        }

        $updatedCompany = $this->companyService->updateCompany($company, $validatedData);
        return new CompanyResource($updatedCompany);
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/company/{id}",
     *     summary="Eliminar company por ID",
     *     tags={"Company"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID de la compañía que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Company eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Company eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Company No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Company No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {
        $deleted = $this->companyService->getCompanyById($id);

        if (! $deleted) {
            return response()->json([
                'error' => 'Company No Encontrado.',
            ], 404);
        }

        $deleted = $this->companyService->destroyById($id);

        return response()->json([
            'message' => 'Company eliminado exitosamente',
        ], 200);
    }
}
