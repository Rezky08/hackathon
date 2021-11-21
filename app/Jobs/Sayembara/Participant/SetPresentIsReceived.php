<?php

namespace App\Jobs\Sayembara\Participant;

use App\Events\Sayembara\PresentWasReceivedByWinner;
use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Sayembara;
use App\Models\Sayembara\Participant;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;

class SetPresentIsReceived
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Sayembara
     */
    public Sayembara $sayembara;

    /**
     * @var Participant
     */
    public Participant $participant;

    /**
     * @var User
     */
    public User $user;

    /**
     * @var Sayembara\Winner
     */
    public Sayembara\Winner $winner;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Authenticatable $user,Sayembara $sayembara)
    {
        /** @var User $user */
        $this->user = $user;
        $this->sayembara = $sayembara;

        /** @var Participant $participant */
        $participant = $this->sayembara->participants()->where([
            'user_id' => $this->user->id
        ])->first();

        $this->participant = $participant;
        throw_if(!$this->participant->exists,Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_PARTICIPANT));
        throw_if(!$this->participant->winner()->exists(),Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_WINNER));
        throw_if($this->participant->winner()->getQuery()->where('present_is_received',true)->exists(),Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_PRESENT_WAS_RECEIVED));

    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        /** @var Sayembara\Winner $winner */
        $winner = $this->participant->winner()->first();
        $this->winner = $winner;
        $this->winner->present_is_received = true;
        if ($this->winner->save()){
            event(new PresentWasReceivedByWinner($this->winner));
        }

        return $this->winner->present_is_received;
    }
}
