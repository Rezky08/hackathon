<?php

namespace App\Jobs\Sayembara;

use App\Events\Sayembara\ExistingSayembaraDeleted;
use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Sayembara;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteExistingSayembara
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Sayembara  */
    public Sayembara $sayembara;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sayembara $sayembara)
    {
        $this->sayembara = $sayembara;
        throw_if($this->sayembara->winners()->exists(),Error::make(Response::CODE_ERROR_FORBIDDEN_SAYEMBARA_DELETE,[
            'message' => __("sayembara :title already have winner",[
                'title' => $this->sayembara->detail->title
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
        DB::beginTransaction();
        try {

            $this->sayembara->detail->delete();

            $this->sayembara->delete();

            event(new ExistingSayembaraDeleted($this->sayembara));

            DB::commit();

        }catch (\Exception $e){
            DB::rollBack();
            throw Error::make(Response::CODE_ERROR_DATABASE_TRANSACTION);
        }

        return $this->sayembara->exists;
    }
}
