<?php
namespace App\Actions;
use App\Exceptions\Error;
use App\Http\Response;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;

class AccountAuthentication{

    protected Request $request;

    const COLUMN_AUTH = 'email';

    const TOKEN_TYPE_USER_LOGIN = 'USER_LOGIN';

    public User $user;

    public Builder $query;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->query = User::query();

    }

    public function auth(){
        try {
            /** @var User $user */
            $user = $this->query->where(self::COLUMN_AUTH,$this->request->get(self::COLUMN_AUTH))->firstOrFail();
            $this->user = $user;
        }catch (\Exception $e){
            throw Error::make(Response::CODE_ERROR_UNAUTHENTICATED);
        }

        /** Password Check */
        throw_if(!Hash::check($this->request->get('password'),$this->user->getAuthPassword()),Error::make(Response::CODE_ERROR_UNAUTHENTICATED));

    }

    public function attempt(){

        $this->auth();

        $token = $this->user->createToken(self::TOKEN_TYPE_USER_LOGIN);

        return [
            'token'=>$token->plainTextToken
        ];
    }
}
