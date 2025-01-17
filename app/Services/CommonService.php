<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class CommonService
{

    public function store_photo(array $data, Object $object, String $name_folder)
    {
        $ruta = "https://develop.garzasoft.com/Gia-Backend/public";
        if (isset($data['route']) && $data['route'] instanceof \Illuminate\Http\UploadedFile) {
            $timestamp = now()->format('Ymd_His');
            $extension = $data['route']->getClientOriginalExtension();
            $fileName  = "{$object->id}_{$timestamp}.{$extension}";
            $filePath  = $data['route']->storeAs($name_folder, $fileName, 'public');
            $object->update(['route' => $ruta . Storage::url($filePath)]);
        }
    }

    public function update_photo(array $data, Object $object, String $name_folder):string
    {
        $ruta = "https://develop.garzasoft.com/Gia-Backend/public";
    
        // Verificar si existe una ruta de foto anterior y eliminarla
        if (!empty($object->route)) {
            $oldPath = str_replace($ruta . '/storage/', '', $object->route);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
    
        // Subir y guardar la nueva imagen si se proporciona
        if (isset($data['route']) && $data['route'] instanceof \Illuminate\Http\UploadedFile) {
            $timestamp = now()->format('Ymd_His');
            $extension = $data['route']->getClientOriginalExtension();
            $fileName  = "{$object->id}_{$timestamp}.{$extension}";
            $filePath  = $data['route']->storeAs($name_folder, $fileName, 'public');
        }
        return $ruta . Storage::url($filePath);
    }
    
     

}
