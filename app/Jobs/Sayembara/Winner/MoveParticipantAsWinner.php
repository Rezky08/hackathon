<?php

namespace App\Jobs\Sayembara\Winner;

use App\Events\Sayembara\ExistingParticipantWin;
use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Sayembara;
use App\Models\Sayembara\Participant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;

class MoveParticipantAsWinner
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Participant
     */
    public Participant $participant;

    /**
     * @var Sayembara\Winner
     */
    public Sayembara\Winner $winner;

    /**
     * @var User
     */
    public User $user;

    /**
     * @var Sayembara
     */
    public Sayembara $sayembara;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Authenticatable $user,Sayembara $sayembara,Participant $participant)
    {
        $this->participant = $participant;
        $this->sayembara = $sayembara;
        $this->winner = new Sayembara\Winner();

        /** @var User $user */
        $this->user = $user;

        Validator::make([
            'participant_id'=>$this->participant->id
        ],[
            'participant_id'=>['unique:sayembara_winners,sayembara_participant_id']
        ],[
            'participant_id.unique'=>"participant ".$this->participant->user->name." already win"
        ])->validate();

        throw_if(!$this->participant->task_is_verified,Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_WINNER));
        throw_if($this->sayembara->user->id !== $this->user->id,Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_OWNER));
        throw_if($this->sayembara->id != $this->participant->sayembara->id,Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_PARTICIPANT));
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        /** @var Sayembara\Winner $winner */
        $winner = $this->participant->winner()->create();
        $this->winner = $winner;

        if ($this->winner->exists){
            event(new ExistingParticipantWin($this->winner));
        }
        return $this->winner->exists;
    }
}
