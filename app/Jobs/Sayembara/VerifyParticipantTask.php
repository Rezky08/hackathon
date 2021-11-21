<?php

namespace App\Jobs\Sayembara;

use App\Events\Sayembara\ExistingParticipantTaskVerified;
use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Sayembara;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class VerifyParticipantTask
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Authenticatable
     */
    public Authenticatable $user;

    /**
     * @var Sayembara
     */
    public Sayembara $sayembara;

    /**
     * @var Sayembara\Participant
     */
    public Sayembara\Participant $participant;

    /**
     * @var bool|mixed
     */
    protected bool $verify;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Authenticatable $user,Sayembara $sayembara,Sayembara\Participant $participant,$verify = true)
    {
        $this->sayembara = $sayembara;
        $this->participant = $participant;
        $this->user = $user;
        $this->verify = $verify;

        throw_if($this->sayembara->user->id !== $this->user->id,Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_OWNER));
        throw_if($this->sayembara->id != $this->participant->sayembara->id,Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_PARTICIPANT));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->participant->task_is_verified = $this->verify;
        if ($this->participant->save() && $this->participant->task_is_verified){
            event(new ExistingParticipantTaskVerified($this->participant));
        }
    }
}
