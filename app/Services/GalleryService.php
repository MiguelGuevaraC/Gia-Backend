<?php
namespace App\Services;

use App\Models\Gallery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryService
{

    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }
    public function getGalleryById(int $id): ?Gallery
    {
        return Gallery::find($id);
    }

    public function createGallery(array $data): array
    {
        $data['user_created_id'] = Auth::id();
        $ruta = env('APP_PROJECT_URL');
        $name_folder = 'gallery';
        $savedGallerys = []; // Inicializar el array para almacenar las imágenes guardadas
    
        try {
            $images = $data['images'] ?? [];
    
            // Si viene una sola imagen (UploadedFile), convertirla en array
            if ($images instanceof \Illuminate\Http\UploadedFile) {
                $images = [['file' => $images]];
            }
    
            // Si viene un solo archivo en formato ['file' => ..., 'name' => ...]
            if (isset($images['file']) && $images['file'] instanceof \Illuminate\Http\UploadedFile) {
                $images = [$images];
            }
    
            // Si viene un array simple de UploadedFile sin claves
            if (isset($images[0]) && $images[0] instanceof \Illuminate\Http\UploadedFile) {
                $images = array_map(fn($file) => ['file' => $file], $images);
            }
    
            foreach ($images as $imageData) {
                $hasFile = isset($imageData['file']) && $imageData['file'] instanceof \Illuminate\Http\UploadedFile;
                $hasName = isset($imageData['name']) && is_string($imageData['name']) && !empty(trim($imageData['name']));
    
                if (!$hasFile || ($hasName && !$hasFile)) {
                    continue;
                }
    
                $file = $imageData['file'];
                $name = $hasName
                    ? Str::slug($imageData['name'])
                    : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    
                $extension = $file->getClientOriginalExtension();
                $data['name_image'] = "{$name}.{$extension}";
                $fileName = Str::uuid() . ".{$extension}";
                $filePath = $file->storeAs($name_folder, $fileName, 'public');
                $data['route'] = $ruta . Storage::url($filePath);
    
                $Gallery = Gallery::create($data);
                $savedGallerys[] = $Gallery;
            }
    
        } catch (\Exception $e) {
            Log::error('Error al subir imagen: ' . $e->getMessage());
            return [];
        }
    
        return $savedGallerys;
    }
    

    public function updateGallery($Gallery, array $data)
    {
        $filteredData = array_intersect_key($data, $Gallery->getAttributes());
        $Gallery->update($filteredData);
        return $Gallery;
    }


    public function destroyById($id)
    {
        $Gallery = Gallery::find($id);

        if (! $Gallery) {
            return false;
        }
        return $Gallery->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
