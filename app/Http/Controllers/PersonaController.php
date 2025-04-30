<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest\StorePersonRequest;
use App\Http\Requests\PersonRequest\UpdatePersonRequest;
use App\Http\Requests\PersonRequest\IndexPersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Services\PersonService;

class PersonaController extends Controller
{

    protected $personService;

    public function __construct(PersonService $personService)
    {
        $this->personService = $personService;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/person",
     *     summary="Obtener información con filtros y ordenamiento",
     *     tags={"Person"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="username", in="query", description="Filtrar por nombre de la persona", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="person.name", in="query", description="Filtrar por nombre de la persona", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="person.business_name", in="query", description="Filtrar por nombre de la empresa", required=false, @OA\Schema(type="string")),


     *     @OA\Parameter(name="from", in="query", description="Fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200,description="Lista de Empresas",@OA\JsonContent(ref="#/components/schemas/Person")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(type="object", @OA\Property(property="error", type="string")))
     * )
     */

    public function index(IndexPersonRequest $request)
    {

        return $this->getFilteredResults(
            Person::class,
            $request,
            Person::filters,
            Person::sorts,
            PersonResource::class
        );
    }

       /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/person/{id}",
     *     summary="Obtener detalles de una persona por ID",
     *     tags={"Person"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la persona", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="persona encontrada", @OA\JsonContent(ref="#/components/schemas/Person")),
     *     @OA\Response(response=404, description="Persona no encontrada", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Persona no encontrada")))
     * )
     */

     public function show($id)
     {
         $person = $this->personService->getPersonById($id);
 
         if (!$person) {
             return response()->json([
                 'error' => 'Persona no encontrada',
             ], 404);
         }
 
         return new PersonResource($person);
     }
 
     /**
      * @OA\Post(
      *     path="/Gia-Backend/public/api/person",
      *     summary="Crear una nueva persona",
      *     tags={"Person"},
      *     security={{"bearerAuth": {}}},
      *     @OA\RequestBody(required=true, @OA\JsonContent(type="object", required={"name", "numberDocument"}, @OA\Property(property="name", type="string", example="persona Ejemplo"))),
      *     @OA\Response(response=200, description="persona creada exitosamente", @OA\JsonContent(ref="#/components/schemas/Person")),
      *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Datos inválidos")))
      * )
      */
 
     public function store(StorePersonRequest $request)
     {
         $person = $this->personService->createPerson($request->validated());
         return new PersonResource($person);
     }
 
     /**
      * @OA\Put(
      *     path="/Gia-Backend/public/api/person/{id}",
      *     summary="Actualizar la información de una persona",
      *     tags={"Person"},
      *     security={{"bearerAuth": {}}},
      *     @OA\Parameter(name="id", in="path", description="ID de la persona a actualizar", required=true, @OA\Schema(type="integer", example=1)),
      *     @OA\RequestBody(required=true, @OA\JsonContent(type="object", @OA\Property(property="name", type="string", example="persona Ejemplo"))),
      *     @OA\Response(response=200, description="persona actualizada exitosamente", @OA\JsonContent(ref="#/components/schemas/Person")),
      *     @OA\Response(response=404, description="Persona no encontrada", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Persona no encontrada"))),
      *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Datos inválidos")))
      * )
      */
 
     public function update(UpdatePersonRequest $request, $id)
     {
         $validatedData = $request->validated();
 
         $person = $this->personService->getPersonById($id);
         if (!$person) {
             return response()->json([
                 'error' => 'Persona no encontrada',
             ], 404);
         }
 
         $updatedCompany = $this->personService->updatePerson($person, $validatedData);
         return new PersonResource($updatedCompany);
     }
 
     /**
      * @OA\Delete(
      *     path="/Gia-Backend/public/api/person/{id}",
      *     summary="Eliminar persona por ID",
      *     tags={"Person"},
      *     security={{"bearerAuth": {}}},
      *     @OA\Parameter(name="id", in="path", description="ID de la persona", required=true, @OA\Schema(type="integer", example=1)),
      *     @OA\Response(response=200, description="persona eliminada exitosamente", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="persona eliminada exitosamente"))),
      *     @OA\Response(response=404, description="Persona no encontrada", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Persona no encontrada")))
      * )
      */
 
     public function destroy($id)
     {
         
        $deleted = $this->personService->getPersonById($id);
         if (!$deleted) {
             return response()->json([
                 'error' => 'Persona no encontrada.',
             ], 404);
         }
 
         $deleted = $this->personService->destroyById($id);
         return response()->json([
             'message' => 'Persona eliminada exitosamente',
         ], 200);
     }

}
