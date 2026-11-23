<?php

namespace App\Http\Controllers\Auth;

use App\Services\RegistrationMailQueue;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait RegistersPartners
{
    use RegistersUsers;

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = DB::transaction(function () use ($request) {
            RegistrationMailQueue::ensureTransactional();
            $user = $this->create($request->all());
            event(new Registered($user));
            RegistrationMailQueue::send($user->email, $user->name, $this->registrationCategory, 'received');

            return $user;
        });

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }
}
