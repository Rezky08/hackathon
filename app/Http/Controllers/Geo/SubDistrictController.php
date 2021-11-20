<?php

namespace App\Http\Controllers\Geo;

use App\Helpers\GeoHelper;
use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Models\District;
use App\Models\SubDistrict;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SubDistrictController extends Controller
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = SubDistrict::query();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request){

        $request->whenHas('province',fn($value)=>GeoHelper::searchByProvince($this->query,$value));
        $request->whenHas('city',fn($value)=>GeoHelper::searchByCity($this->query,$value));
        $request->whenHas('district',fn($value)=>GeoHelper::searchByDistrict($this->query,$value));
        $request->whenHas('name',fn($value)=>GeoHelper::searchByName($this->query,$value));

        return new Response(Response::CODE_SUCCESS,$this->paginate($request,$this->query));
    }
}
