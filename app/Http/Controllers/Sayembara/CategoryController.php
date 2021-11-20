<?php

namespace App\Http\Controllers\Sayembara;

use App\Helpers\SayembaraHelper;
use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Models\Sayembara\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected Builder $query;
    public function __construct()
    {
        $this->query = Category::query();
    }

    public function index(Request $request){
        $request->whenHas('q',fn($value)=>SayembaraHelper::searchByName($this->query,$value));
        return new Response(Response::CODE_SUCCESS,$this->paginate($request,$this->query));
    }
}
