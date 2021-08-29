<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function welcome(Request $req)
    {
        $req->validate([
            'phone' => ['required', 'regex:/^(\+98|0)?9\d{9}$/'],
            'email' => 'email',
            'password' => ['required']
        ]);
        $phone = substr($req['phone'], -10);
        $user = User::where('phone',$phone)->first();
        if (is_null($user)) {
            $user = new User;
            $user['phone'] = $phone;
            $user['password'] = Hash::make($req['password']);
            $user['access_token'] = $user->createToken('BookApi')->accessToken;
            $user->save();
            $user = User::where('phone',$phone)->first();
        }
        if (!(Hash::check($req['password'], $user['password']))) {
            abort(401, 'wrong password.');
        }
        return $user;
    }
}
