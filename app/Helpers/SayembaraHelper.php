<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SayembaraHelper{

    static public function searchByName(Builder &$query,$value){
        $query->where('name','ilike','%'.$value.'%');
    }
    static public function limitAgeValidate($rules,$value){
        $value = [
            'age' => $value
        ];
        $rules = [
            'age'=> $rules
        ];

        $validator = Validator::make($value,$rules);

        return $validator->fails();
    }

    static public function limitGenderValidate($rules,$value){
        return in_array($value,$rules);
    }

    static public function limitGeoValidate($rules,$value){
        $rules = [
            'province'=>Rule::in($rules['province']),
            'city'=>Rule::in($rules['city']),
            'district'=>Rule::in($rules['district']),
            'sub_district'=>Rule::in($rules['sub_district']),
        ];
        $validator = Validator::make($value,$rules);
        return $validator->fails();

    }
}
