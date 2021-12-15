<?php

namespace App\Http\Controllers\Geo;

use App\Helpers\GeoHelper;
use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Models\Province;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\This;

class ProvinceController extends Controller
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = Province::query();
    }

    public function index(Request $request){
        $request->whenHas('name',fn($value)=>  GeoHelper::searchByName($this->query,$value));
        $request->whenHas('id',fn($value)=>  GeoHelper::searchById($this->query,$value,Province::class));
        return new Response(Response::CODE_SUCCESS,$this->paginate($request,$this->query));
    }
}
