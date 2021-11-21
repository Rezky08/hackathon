<?php

namespace App\Http\Controllers;

use App\Http\Response;
use App\Jobs\Attachment\CreateNewAttachment;
use App\Models\Attachment;
use App\Models\Sayembara;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function store(Request $request){
        $job = new CreateNewAttachment($request->allFiles());
        $this->dispatch($job);
        /** @var Attachment $attachment */
        $attachment = $job->attachment;
        return new Response(Response::CODE_DATA_CREATED,$attachment);
    }
}
