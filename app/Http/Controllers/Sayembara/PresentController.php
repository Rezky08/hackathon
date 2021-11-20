<?php

namespace App\Http\Controllers\Sayembara;

use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Models\Sayembara\Detail;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PresentController extends Controller
{
    protected Collection $presentTypes;
    public function __construct()
    {
        $this->presentTypes = new Collection(Detail::getAvaliablePresentType());
    }

    function getPresentType(Request $request){
        $request->whenHas('q',fn($value)=>$this->searchType($value));
        return new Response(Response::CODE_SUCCESS,$this->presentTypes);
    }

    function searchType($value){
        $this->presentTypes = $this->presentTypes->filter(fn($type)=>str_contains($type,$value));
        $this->presentTypes = $this->presentTypes->values();
    }
}
