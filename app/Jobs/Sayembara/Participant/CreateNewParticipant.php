<?php

namespace App\Jobs\Sayembara\Participant;

use App\Events\Sayembara\NewParticipantJoined;
use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Sayembara;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateNewParticipant
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
     * @var array
     */
    public array $attributes;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Authenticatable $user,Sayembara $sayembara)
    {
        $this->user = $user;
        $this->sayembara = $sayembara;
        $this->participant = new Sayembara\Participant();
        $this->attributes = [
            'user_id' => $this->user->id,
            'sayembara_id' => $this->sayembara->id
        ];

        $this->attributes = Validator::make($this->attributes,[
            'user_id' => [Rule::unique('sayembara_participants')->where(function (Builder $query){
                return $query
                    ->where('user_id',$this->attributes['user_id'])
                    ->where('sayembara_id',$this->attributes['sayembara_id']);
            })],
            'sayembara_id' => [Rule::unique('sayembara_participants')->where(function (Builder $query){
                return $query
                    ->where('user_id',$this->attributes['user_id'])
                    ->where('sayembara_id',$this->attributes['sayembara_id']);
            })]
        ])->validate();

        throw_if($this->sayembara->user->id == $this->user->id,Error::make(Response::CODE_ERROR_FORBIDDEN_SAYEMBARA_JOIN));


    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle()
    {
        $this->participant->fill($this->attributes);
        if ($this->participant->save()){
            event(new NewParticipantJoined($this->participant));
        }

        return $this->participant->exists;
    }
}
