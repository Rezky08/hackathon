<?php

namespace App\Http\Controllers\Geo;

use App\Helpers\GeoHelper;
use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Models\City;
use App\Models\District;
use App\Models\Province;
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

        $request->whenHas('district',fn($value)=>GeoHelper::searchByDistrict($this->query,$value));
        $request->whenHas('district_id',fn($value)=>GeoHelper::searchByDistrict($this->query,$value,District::class));
        $request->whenHas('name',fn($value)=>GeoHelper::searchByName($this->query,$value));
        $request->whenHas('id',fn($value)=>GeoHelper::searchById($this->query,$value,SubDistrict::class));

        return new Response(Response::CODE_SUCCESS,$this->paginate($request,$this->query));
    }
}
