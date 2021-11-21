<?php

namespace App\Jobs\Sayembara\Participant;

use App\Events\Sayembara\ExistingParticipantOut;
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

class DeleteExistingParticipant
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
        $this->sayembara = $sayembara;
        $this->user = $user;
        $this->attributes = [
            'user_id' => $this->user->id,
            'sayembara_id' => $this->sayembara->id,

        ];

        $validator = Validator::make($this->attributes,[
            'user_id' => [Rule::exists('sayembara_participants')->where(function (Builder $query){
                return $query
                    ->where('user_id',$this->attributes['user_id'])
                    ->where('sayembara_id',$this->attributes['sayembara_id']);
            })]
        ]);

        throw_if($validator->fails(),Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_PARTICIPANT));

        /** @var Sayembara\Participant $participant */
        $participant =  Sayembara\Participant::query()
            ->where('user_id',$this->attributes['user_id'])
            ->where('sayembara_id',$this->attributes['sayembara_id'])
            ->firstOrFail();

        $this->participant = $participant;

        throw_if($this->participant->winner()->exists(),Error::make(Response::CODE_ERROR_FORBIDDEN_SAYEMBARA_OUT,[
            'message'=>__(':name already win sayembara :title',[
                'name' => $participant->user->name,
                'title'=>$sayembara->detail->title
            ])
        ]));

    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle()
    {
        $this->participant->delete();
        event(new ExistingParticipantOut($this->participant));
        return !$this->participant->exists;
    }
}
