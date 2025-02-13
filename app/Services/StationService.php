<?php
namespace App\Services;

use App\Models\Station;

class StationService
{

    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }
    public function getStationById(int $id): ?Station
    {
        return Station::find($id);
    }

    public function createStation(array $data): Station
    {
        $station = Station::create($data);
        $this->commonService->store_photo($data, $station, 'stations');
        return $station;
    }

    public function updateStation($Station, array $data)
    {
        if (isset($data['route'])) {
            $data['route'] = $this->commonService->update_photo($data, $Station, 'stations');
        }
        $Station->update($data);
        return $Station;
    }

    public function updateStationstatus($Station, string $status)
    {
        $Station->update(["status" => $status]);
        return $Station;
    }

    public function destroyById($id)
    {
        $Station = Station::find($id);

        if (! $Station) {
            return false;
        }
        return $Station->delete(); // Devuelve true si la eliminación fue exitosa
    }

}
