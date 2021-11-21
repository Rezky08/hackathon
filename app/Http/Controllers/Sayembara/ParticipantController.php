<?php

namespace App\Http\Controllers\Sayembara;

use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Jobs\Sayembara\Participant\CreateNewParticipant;
use App\Jobs\Sayembara\Participant\DeleteExistingParticipant;
use App\Jobs\Sayembara\Participant\SetPresentIsReceived;
use App\Models\Sayembara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipantController extends Controller
{
    public function joinSayembara(Request $request,Sayembara $sayembara){

        $job = new CreateNewParticipant(Auth::user(),$sayembara);
        $this->dispatch($job);

        /** @var Sayembara\Participant $participant */
        $participant = $job->participant;
        return new Response(Response::CODE_DATA_CREATED,$participant);
    }
    public function outSayembara(Request $request,Sayembara $sayembara){
        $job = new DeleteExistingParticipant(Auth::user(),$sayembara);
        $this->dispatch($job);

        /** @var Sayembara\Participant $participant */
        $participant = $job->participant;
        return new Response(Response::CODE_SUCCESS,$participant);
    }
    public function receiveSayembaraPresent(Request $request,Sayembara $sayembara){
        $job = new SetPresentIsReceived(Auth::user(),$sayembara);
        $this->dispatch($job);

        /** @var Sayembara\Winner $winner */
        $winner = $job->winner;
        return new Response(Response::CODE_SUCCESS,$winner);
    }
}
