<?php

namespace App\Http\Middleware;

use App\Exceptions\Error;
use App\Http\Response;
use App\Models\Sayembara;
use App\Models\User;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateSayembaraParticipant
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
        /** @var Builder $query */
        $query = $sayembara->participants()->getQuery()->where([
            'user_id'=>$user->id
        ]);
        /** @var Sayembara\Participant $participant */
        $participant = $query->first();
        throw_if(!$participant,Error::make(Response::CODE_ERROR_INVALID_SAYEMBARA_PARTICIPANT));

        return $next($request);
    }
}
