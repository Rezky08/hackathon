<?php

namespace App\Http\Controllers\Sayembara;

use App\Http\Controllers\Controller;
use App\Http\Resources\SayembaraResource;
use App\Http\Response;
use App\Jobs\Sayembara\CreateNewSayembara;
use App\Jobs\Sayembara\DeleteExistingSayembara;
use App\Jobs\Sayembara\UpdateExistingSayembara;
use App\Models\Sayembara;
use Illuminate\Http\Request;

class SayembaraController extends Controller
{
    public function createNewSayembara(Request $request){
        $job = new CreateNewSayembara($request->all());
        $this->dispatch($job);
        return new Response(Response::CODE_DATA_CREATED);
    }
    public function updateExistingSayembara(Request $request,Sayembara $sayembara){
        $job = new UpdateExistingSayembara($sayembara,$request->all());
        $this->dispatch($job);
        $sayembara = $job->sayembara;
        return new Response(Response::CODE_SUCCESS,SayembaraResource::make($sayembara));
    }
    public function deleteExistingSayembara(Request $request,Sayembara $sayembara){
        $job = new DeleteExistingSayembara($sayembara);
        $this->dispatch($job);
        $sayembara = $job->sayembara;
        return new Response(Response::CODE_SUCCESS,SayembaraResource::make($sayembara));
    }
}
