<?php

namespace App\Http\Middleware;

use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Sayembara;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateSayembaraOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Sayembara $sayembara */
        $sayembara = $request->route('sayembara_id');
        /** @var User $user */
        $user = Auth::user();
        throw_if($sayembara->user->id !== $user->id,Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_OWNER));

        return $next($request);
    }
}
