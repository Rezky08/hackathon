<?php
namespace App\Actions;
use App\Models\User;
use Symfony\Component\HttpFoundation\Request;

class AccountAuthentication{

    protected Request $request;

    const COLUMN_AUTH = 'email';

    const TOKEN_TYPE_USER_LOGIN = 'USER_LOGIN';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function attempt(){
        $query = User::query()->where(self::COLUMN_AUTH,$this->request->get(self::COLUMN_AUTH));
        /** @var User $user */
        $user = $query->firstOrFail();
        $token = $user->createToken(self::TOKEN_TYPE_USER_LOGIN);

        return [
            'token'=>$token->plainTextToken
        ];
    }
}
