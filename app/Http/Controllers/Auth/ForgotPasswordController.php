<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    use PartnerPasswordCredentials;

    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('throttle:10,1')->only('sendResetLinkEmail');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        DB::transaction(function () use ($request) {
            $this->broker()->sendResetLink($this->partnerCredentials($request));
        });

        return back()->with('status', 'Jika email terdaftar sebagai supplier atau distributor, tautan reset akan dikirim. Periksa kotak masuk dan spam.');
    }
}
