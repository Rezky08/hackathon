<?php
namespace App\Helpers;

use App\Models\Province;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GeoHelper{

    static public function searchByName(Builder &$query,$value){
        $query->where('name','ilike','%'.$value.'%');
    }
    static public function searchById(Builder &$query,$value,string $modelClass=null){
        $keyName = 'id';
        if ($modelClass){
            /** @var Model $model */
            $model = new $modelClass();
            $keyName = $model->getKeyName();
        }
        $query->where($keyName,$value);
    }

    static public function searchByProvince(Builder &$query,$value,$isId=false){

        $query->whereHas('province',function ($query) use($value,$isId) {
            if ($isId){
                self::searchById($query,$value,Province::class);
            }else{
                self::searchByName($query,$value);
            }
        });
        $query->with('province');
    }

    static public function searchByCity(Builder &$query,$value,$isId=false){
        $query->whereHas('city',function ($query) use($value,$isId) {
            if ($isId){
                self::searchById($query,$value,Province::class);
            }else{
                self::searchByName($query,$value);
            }
        });
        $query->with('city');
    }

    static public function searchByDistrict(Builder &$query,$value,$isId=false){
        $query->whereHas('district',function ($query) use($value,$isId) {
            if ($isId){
                self::searchById($query,$value,Province::class);
            }else{
                self::searchByName($query,$value);
            }
        });
        $query->with('district');
    }
}
