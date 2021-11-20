<?php

namespace App\Jobs\Sayembara;

use App\Events\Sayembara\ExistingSayembaraUpdated;
use App\Events\Sayembara\NewSayembaraCreated;
use App\Exceptions\Error;
use App\Http\Response;
use App\Jobs\Sayembara\Detail\UpdateDetailExistingSayembara;
use App\Models\Sayembara;
use App\Models\Sayembara\Detail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateExistingSayembara
{
    public Sayembara $sayembara;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sayembara $sayembara,array $attributes=[])
    {
        $this->sayembara = $sayembara;
        $rules = [
            'start_date'=>['filled','date'],
            'end_date'=>['filled','date'],
            'is_open'=>['nullable','bool'],
        ];
        $this->attributes = Validator::make($attributes,$rules)->validate();
        $this->sayembaraDetailJob = new UpdateDetailExistingSayembara($sayembara,$attributes);
    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle()
    {
        /** database multiple transaction */
        DB::beginTransaction();
        try {
            foreach ($this->attributes as $key => $value){
                $this->sayembara->{$key}= $value;
            }
            $this->sayembara->save();

            throw_if(!$this->sayembara->exists,Error::make(Response::CODE_ERROR_DATABASE_TRANSACTION));

            dispatch($this->sayembaraDetailJob);

            $this->sayembara->refresh();

            event(new ExistingSayembaraUpdated($this->sayembara));

            DB::commit();

        }catch (\Exception $e){
            DB::rollBack();
            throw Error::make(Response::CODE_ERROR_DATABASE_TRANSACTION,[],$e);
        }

        return $this->sayembara->exists;

    }
}
