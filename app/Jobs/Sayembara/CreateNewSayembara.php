<?php

namespace App\Jobs\Sayembara;

use App\Models\Sayembara\Detail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateNewSayembara implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    protected array $attributes;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        $rules = [
            'province'=>['required','filled','exists:provinces,id'],
            'city'=>['required','filled','exists:cities,id'],
            'district'=>['nullable','exists:districts,id'],
            'thumbnail'=>['nullable','exists:attachments,id'],
            'title'=>['required','filled'],
            'present_type'=>['required','filled',Rule::in(Detail::getAvaliablePresentType())],
            'present_value' =>['required','filled',/*Todo: add validation when present_type is money*/],
            'category'=>['required','filled','exists:sayembara_categories,name'],
            'max_participant' => ['required','filled','numeric'],
            'max_winner' => ['required','filled','numeric'],
            'content' => ['required','filled'],
            'limit' => ['nullable','json'],
        ];
        $this->attributes = Validator::make($attributes,$rules)->validate();
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
