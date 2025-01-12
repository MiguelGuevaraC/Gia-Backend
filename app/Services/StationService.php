<?php
namespace App\Services;

use App\Http\Resources\StationResource;
use App\Models\Station;
use Illuminate\Support\Facades\Http;

class StationService
{

    public function getStationById(int $id): ?Station
    {
        return Station::find($id);
    }

    public function createStation(array $data): Station
    {
        return Station::create($data);
    }

    public function updateStation($Station, array $data)
    {
        $Station->update($data);
        return $Station;
    }

    public function destroyById($id)
    {
        $Station = Station::find($id);

        if (!$Station) {
            return false;
        }
        return $Station->delete(); // Devuelve true si la eliminación fue exitosa
    }

   
    

}
