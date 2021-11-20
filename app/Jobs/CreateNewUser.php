<?php

namespace App\Jobs;

use App\Events\NewUserCreated;
use App\Exceptions\Error;
use App\Http\Response;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;

class CreateNewUser
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $attributes;

    public User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->user = new User();

        $rules = [
            'name'=>['required','filled'],
            'email' => ['required','filled','email','unique:users,email'],
            'password_confirmation' =>['required','filled'],
            'password' => ['required','filled','confirmed'],
        ];

        $this->attributes = Validator::make($data,$rules)->validate();
    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle()
    {
        $this->user->fill($this->attributes);

        if ($this->user->save()){
            event(new NewUserCreated($this->user));
        }
        return $this->user->exists;
    }
}
