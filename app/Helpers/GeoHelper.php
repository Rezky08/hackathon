<?php
namespace App\Helpers;

use App\Models\City;
use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GeoHelper{

    static public function searchByName(Builder &$query,$value,string $modelClass=null){
        if ($modelClass){
            /** @var Model $model */
            $model = new $modelClass();
            $tableName = $model->getTable();
            $query->where("{$tableName}.name",'ilike','%'.$value.'%');
        }else{
            $query->where("name",'ilike','%'.$value.'%');
        }
    }
    static public function searchById(Builder &$query,$value,string $modelClass=null){
        $keyName = 'id';
        if ($modelClass){
            /** @var Model $model */
            $model = new $modelClass();
            $keyName = $model->getKeyName();
            $tableName = $model->getTable();
            $query->where("{$tableName}.{$keyName}",$value);
        }else{
            $query->where("id",$value);
        }
    }

    static public function searchByProvince(Builder &$query,$value,$isId=false){
        $modelClass = Province::class;
        $query->whereHas('province',function ($query) use($value,$isId,$modelClass) {
            if ($isId){
                self::searchById($query,$value,$modelClass);
            }else{
                self::searchByName($query,$value,$modelClass);
            }
        });
        $query->with('province');
    }

    static public function searchByCity(Builder &$query,$value,$isId=false){
        $modelClass = City::class;
        $query->whereHas('city',function ($query) use($value,$isId,$modelClass) {
            if ($isId){
                self::searchById($query,$value,$modelClass);
            }else{
                self::searchByName($query,$value,$modelClass);
            }
        });
        $query->with('city');
    }

    static public function searchByDistrict(Builder &$query,$value,$isId=false){
        $modelClass = District::class;
        $query->whereHas('district',function ($query) use($value,$isId,$modelClass) {
            if ($isId){
                self::searchById($query,$value,$modelClass);
            }else{
                self::searchByName($query,$value,$modelClass);
            }
        });
        $query->with('district');
    }
}
