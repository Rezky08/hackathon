<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

class GeoHelper{

    static public function searchByName(Builder &$query,$value){
        $query->where('name','ilike','%'.$value.'%');
    }

    static public function searchByProvince(Builder &$query,$value){

        $query->whereHas('province',function ($query) use($value) {
            self::searchByName($query,$value);
        });
        $query->with('province');
    }

    static public function searchByCity(Builder &$query,$value){
        $query->whereHas('city',function ($query) use($value) {
            self::searchByName($query,$value);
        });
        $query->with('city');
    }

    static public function searchByDistrict(Builder &$query,$value){
        $query->whereHas('district',function ($query) use($value) {
            self::searchByName($query,$value);
        });
        $query->with('district');
    }
}
