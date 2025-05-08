<?php
namespace App\Http\Controllers;

use App\Http\Requests\GalleryRequest\IndexGalleryRequest;
use App\Http\Requests\GalleryRequest\StoreGalleryRequest;
use App\Http\Requests\GalleryRequest\UpdateGalleryRequest;

use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    protected $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/gallery",
     *     summary="Obtener información de promociones con filtros y ordenamiento",
     *     tags={"Gallery"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="name", in="query", description="Filtrar por nombre", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="description", in="query", description="Filtrar por descripción", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="precio", in="query", description="Filtrar por precio", required=false, @OA\Schema(type="number", format="float")),
     *     @OA\Parameter(name="date_start", in="query", description="Filtrar por fecha de inicio", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="date_end", in="query", description="Filtrar por fecha de fin", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="stock", in="query", description="Filtrar por stock", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="status", in="query", description="Filtrar por estado (activo, inactivo)", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from", in="query", description="Filtrar desde esta fecha (creación)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to", in="query", description="Filtrar hasta esta fecha (creación)", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de Imagen Galeriaes", @OA\JsonContent(ref="#/components/schemas/Gallery")),
     *     @OA\Response(response=422, description="Validación fallida", @OA\JsonContent(type="object", @OA\Property(property="error", type="string")))
     * )
     */
    public function index(IndexGalleryRequest $request)
    {
        return $this->getFilteredResults(
            Gallery::class,
            $request,
            Gallery::filters,
            Gallery::sorts,
            GalleryResource::class
        );
    }

    /**
     * @OA\Get(
     *     path="/Gia-Backend/public/api/gallery/{id}",
     *     summary="Obtener detalles de un gallery por ID",
     *     tags={"Gallery"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID de la empresa", required=true, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Gallery encontrada", @OA\JsonContent(ref="#/components/schemas/Gallery")),
     *     @OA\Response(response=404, description="Imagen Galeria No Encontrado", @OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Imagen Galeria No Encontrado")))
     * )
     */

    public function show($id)
    {

        $gallery = $this->galleryService->getGalleryById($id);

        if (! $gallery) {
            return response()->json([
                'error' => 'Imagen Galeria No Encontrado',
            ], 404);
        }

        return new GalleryResource($gallery);
    }

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/gallery",
     *     summary="Crear gallery",
     *     tags={"Gallery"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     * @OA\Schema(ref="#/components/schemas/GalleryRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Gallery creado exitosamente", @OA\JsonContent(ref="#/components/schemas/Gallery")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un gallery asociado.")))
     * )
     */
    public function store(StoreGalleryRequest $request)
    {
        $galleries = $this->galleryService->createGallery($request->validated());
        return GalleryResource::collection($galleries);
    }
    

    /**
     * @OA\Post(
     *     path="/Gia-Backend/public/api/gallery/{id}",
     *     summary="Actualizar Gallery",
     *     tags={"Gallery"},
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
     * @OA\Schema(ref="#/components/schemas/GalleryRequest")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Gallery actualizado exitosamente", @OA\JsonContent(ref="#/components/schemas/Gallery")),
     *     @OA\Response(response=422, description="Error de validación", @OA\JsonContent(@OA\Property(property="error", type="string", example="La persona ya tiene un gallery asociado.")))
     * )
     */

    public function update(UpdateGalleryRequest $request, $id)
    {

        $validatedData = $request->validated();

        $gallery = $this->galleryService->getGalleryById($id);
        if (! $gallery) {
            return response()->json([
                'error' => 'Imagen Galeria No Encontrado',
            ], 404);
        }

        $updatedGallery = $this->galleryService->updateGallery($gallery, $validatedData);
        return new GalleryResource(Gallery::find($updatedGallery->id));
    }

    /**
     * @OA\Delete(
     *     path="/Gia-Backend/public/api/gallery/{id}",
     *     summary="Eliminar gallery por ID",
     *     tags={"Gallery"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id",in="path",description="ID de la compañía que se desea eliminar",required=true,@OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200,description="Gallery eliminada exitosamente",@OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Gallery eliminada exitosamente"))),
     *     @OA\Response(response=404,description="Gallery No Encontrada",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="Gallery No Encontrada"))),
     *     @OA\Response(response=401,description="No autorizado",@OA\JsonContent(type="object", @OA\Property(property="error", type="string", example="No autorizado"))
     *     )
     * )
     **/

    public function destroy($id)
    {

        $deleted = $this->galleryService->getGalleryById($id);
        if (! $deleted) {
            return response()->json([
                'error' => 'Imagen Galeria No Encontrado.',
            ], 404);
        }
        $deleted = $this->galleryService->destroyById($id);
        return response()->json([
            'message' => 'Imagen Galeria eliminado exitosamente',
        ], 200);
    }
}
