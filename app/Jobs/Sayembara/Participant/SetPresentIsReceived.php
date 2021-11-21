<?php

namespace App\Jobs\Sayembara\Participant;

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
        dd(!$this->participant->winner()->exists());
        throw_if(!$this->participant->exists,Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_PARTICIPANT));
        throw_if(!$this->participant->winner()->exists(),Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_PARTICIPANT));

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
