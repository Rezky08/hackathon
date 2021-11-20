<?php

namespace App\Jobs\Sayembara;

use App\Models\Sayembara;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetSayembaraIsOpen
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Sayembara $sayembara;

    public bool $isOpen;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Sayembara $sayembara,$isOpen = true)
    {
        $this->sayembara = $sayembara;
        $this->isOpen = $isOpen;
    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle()
    {
        $this->sayembara->is_open = $this->isOpen;
        $this->sayembara->save();
        return $this->sayembara->wasChanged('is_open');
    }
}
