<?php

namespace App\Http\Controllers\Geo;

use App\Helpers\GeoHelper;
use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = City::query();
    }

    public function index(Request $request){

        $request->whenHas('province',fn($value)=>GeoHelper::searchByProvince($this->query,$value));
        $request->whenHas('province_id',fn($value)=>GeoHelper::searchByProvince($this->query,$value,true));
        $request->whenHas('name',fn($value)=>GeoHelper::searchByName($this->query,$value));
        $request->whenHas('id',fn($value)=>GeoHelper::searchById($this->query,$value,City::class));

        return new Response(Response::CODE_SUCCESS,$this->paginate($request,$this->query));
    }
}
