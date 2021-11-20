<?php

namespace App\Http\Controllers\Sayembara;

use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Jobs\Sayembara\CreateNewSayembara;
use App\Jobs\Sayembara\UpdateExistingSayembara;
use Illuminate\Http\Request;

class SayembaraController extends Controller
{
    public function createNewSayembara(Request $request){
        $job = new CreateNewSayembara($request->all());
        $this->dispatch($job);
        return new Response(Response::CODE_DATA_CREATED);
    }
    public function updateExistingSayembara(Request $request){
        $job = new UpdateExistingSayembara($request->all());
        $this->dispatch($job);
        return new Response(Response::CODE_DATA_CREATED);
    }
}
