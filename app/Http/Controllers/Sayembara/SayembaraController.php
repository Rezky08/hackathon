<?php

namespace App\Http\Controllers\Sayembara;

use App\Http\Controllers\Controller;
use App\Jobs\Sayembara\CreateNewSayembara;
use Illuminate\Http\Request;

class SayembaraController extends Controller
{
    public function createNewSayembara(Request $request){
        $job = new CreateNewSayembara($request->all());
        $this->dispatch($job);
    }
}
