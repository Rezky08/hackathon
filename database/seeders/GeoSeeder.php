<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use App\Models\SubDistrict;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

class GeoSeeder extends Seeder
{
    public function getDataPath($fileName){
        return implode('\\',[__DIR__,"data",$fileName]);
    }

    public function readCsvData($csvFile){
        $file_handle = fopen($csvFile,'r');
        $data = [];
        while (!feof($file_handle)){
            $data[] = fgetcsv($file_handle);
        }
        fclose($file_handle);
        return $data;
    }

    public function storeProvinceData(){
        $fileName = $this->getDataPath('province.csv');
        $data = $this->readCsvData($fileName);
        //for header
        array_shift($data);
        $dataCollection = collect($data);
        $dataCollection = $dataCollection->filter();
        if (Province::query()->count() === $dataCollection->count()){
            return true;
        }

        $this->command->info('Province seeding');
        foreach ($data as $value){
            try {
                if (!$value){
                    continue;
                }
                $dataInsert=[
                    'id'=> $value[0],
                    'name' =>$value[1],
                ];
                Province::query()->firstOrCreate($dataInsert);
            }catch (\Exception $e){
                $this->command->error('Error : '.$e->getMessage());
            }
        }
        $this->command->info('Province was seeded\n');
    }

    public function storeCityData(){
        $fileName = $this->getDataPath('city.csv');
        $data = $this->readCsvData($fileName);
        //for header
        array_shift($data);

        $dataCollection = collect($data);
        $dataCollection = $dataCollection->filter();
        if (City::query()->count() === $dataCollection->count())
            return true;

        $this->command->info('City seeding');
        foreach ($data as $value){
            try {
                if (!$value){
                    continue;
                }
                $dataInsert=[
                    'id'=> $value[0],
                    'name' =>$value[1],
                    'province_id'=>$value[2]
                ];
                Province::query()->findOrFail($dataInsert['province_id']);
                City::query()->firstOrCreate($dataInsert);
            }catch (\Exception $e){
                $this->command->error('Error : '.$e->getMessage());
            }
        }
        $this->command->info('City was seeded\n');
    }

    public function storeDistrictData(){
        $fileName = $this->getDataPath('district.csv');
        $data = $this->readCsvData($fileName);

        //for header
        array_shift($data);

        $dataCollection = collect($data);
        $dataCollection = $dataCollection->filter();
        if (District::query()->count() === $dataCollection->count())
            return true;

        $this->command->info('District seeding');
        foreach ($data as $value){
            try {
                if (!$value){
                    continue;
                }
                $dataInsert=[
                    'id'=> $value[0],
                    'name' =>$value[1],
                    'city_id'=>$value[2]
                ];
                City::query()->findOrFail($dataInsert['city_id']);
                District::query()->firstOrCreate($dataInsert);
            }catch (\Exception $e){
                $this->command->error('Error : '.$e->getMessage());
            }
        }
        $this->command->info('District was seeded\n');
    }

    public function storeSubDistrictData(){
        $fileName = $this->getDataPath('subdistrict.csv');
        $data = $this->readCsvData($fileName);
        //for header
        array_shift($data);

        $dataCollection = collect($data);
        $dataCollection = $dataCollection->filter();
        if (SubDistrict::query()->count() === $dataCollection->count())
            return true;
        $this->command->info('SubDistrict seeding');
        foreach ($data as $value){
            try {
                if (!$value){
                    continue;
                }
                $dataInsert=[
                    'id'=> $value[0],
                    'name' =>$value[1],
                    'district_id'=>str_replace(';','',$value[2])
                ];

                District::query()->findOrFail($dataInsert['district_id']);
                SubDistrict::query()->firstOrCreate($dataInsert);
            }catch (\Exception $e){
                $this->command->error('Error : '.$e->getMessage());
            }
        }
        $this->command->info('SubDistrict was seeded\n');
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->storeProvinceData();
        $this->storeCityData();
        $this->storeDistrictData();
        $this->storeSubDistrictData();
    }
}
