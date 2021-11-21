<?php

namespace App\Http\Controllers;

use App\Actions\AccountAuthentication;
use App\Http\Response;
use App\Jobs\User\CreateNewUser;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index(Request $request){
        $users = $this->paginate($request,User::query());
        return new Response(Response::CODE_SUCCESS,$users);
    }

    public function store(Request $request){
        $job = new CreateNewUser($request->all());
        $this->dispatch($job);
        return new Response(Response::CODE_DATA_CREATED,$job->user);
    }

    public function login(Request $request){
        $attempt = (new AccountAuthentication($request))->attempt();

        return new Response(Response::CODE_SUCCESS,$attempt);
    }
}
