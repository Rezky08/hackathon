<?php

namespace App\Http\Controllers\Sayembara;

use App\Helpers\SayembaraHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\SayembaraResource;
use App\Http\Response;
use App\Jobs\Sayembara\CreateNewSayembara;
use App\Jobs\Sayembara\DeleteExistingSayembara;
use App\Jobs\Sayembara\VerifyParticipantTask;
use App\Jobs\Sayembara\SetSayembaraIsOpen;
use App\Jobs\Sayembara\UpdateExistingSayembara;
use App\Jobs\Sayembara\Winner\MoveParticipantAsWinner;
use App\Models\Sayembara;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SayembaraController extends Controller
{
    protected Builder $query;

    public function __construct()
    {
        $this->query = Sayembara::query();
    }

    public function index(Request $request){
        $request->whenHas('name',fn($value)=>SayembaraHelper::searchByName($this->query,$value));
        return new Response(Response::CODE_SUCCESS,SayembaraResource::collection($this->paginate($request,$this->query)));
    }

    public function createNewSayembara(Request $request){
        $job = new CreateNewSayembara($request->all());
        $this->dispatch($job);
        $sayembara = $job->sayembara;
        return new Response(Response::CODE_DATA_CREATED,SayembaraResource::make($sayembara));
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

    public function setExistingSayembaraStatus(Request $request,Sayembara $sayembara){
        $attributes = Validator::make($request->all(),[
            'is_open'=>['required','filled','bool']
        ])->validate();

        $job = new SetSayembaraIsOpen($sayembara,$attributes['is_open']);
        $this->dispatch($job);
        $sayembara = $job->sayembara;
        return new Response(Response::CODE_SUCCESS,SayembaraResource::make($sayembara));
    }

    public function verifyParticipantTask(Request $request,Sayembara $sayembara,Sayembara\Participant $participant){
        $job = new VerifyParticipantTask(Auth::user(),$sayembara,$participant,$request->verify??true);
        $this->dispatch($job);
        $participant = $job->participant;
        return new Response(Response::CODE_SUCCESS,$participant);
    }

    public function setParticipantAsWinner(Request $request,Sayembara $sayembara,Sayembara\Participant $participant){
        $job = new MoveParticipantAsWinner(Auth::user(),$sayembara,$participant);
        $this->dispatch($job);
        $participant = $job->participant;
        return new Response(Response::CODE_SUCCESS,$participant);
    }
}
