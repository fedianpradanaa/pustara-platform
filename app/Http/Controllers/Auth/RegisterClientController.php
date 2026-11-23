<?php

namespace App\Http\Controllers\Auth;

use App\Logs;
use App\Http\Controllers\Controller;
use App\Services\RegistrationMailQueue;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegisterClientController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RedirectsUsers;
    
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $clientId = (string) Str::uuid();
        $logoBigName = $clientId . '-logo.' . $request->file('logo_big')->getClientOriginalExtension();
        $logoSmallName = $clientId . '-logo_small.' . $request->file('logo_small')->getClientOriginalExtension();

        try {
            DB::transaction(function () use ($request, $clientId, $logoBigName, $logoSmallName) {
                RegistrationMailQueue::ensureTransactional();
                $this->create($request, $clientId, $logoBigName, $logoSmallName);
                RegistrationMailQueue::send($request->email, $request->name, 'client', 'received');
            });
        } catch (\Throwable $e) {
            if (! Storage::disk('public')->delete(['images/logo/' . $logoBigName, 'images/logo/' . $logoSmallName])) {
                Log::error('Logo pendaftaran gagal dibersihkan setelah pendaftaran gagal.', ['client_id' => $clientId]);
            }
            throw $e;
        }

        return $request->wantsJson() ? new JsonResponse([], 201) : redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'name_apps' => ['required', 'string', 'max:150'],
            'web_add' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:200'],
            'country' => ['required', 'string', 'max:25'],
            'province' => ['required', 'string', 'max:25'],
            'regency' => ['required', 'string', 'max:25'],
            'district' => ['required', 'string', 'max:25'],
            'subdistrict' => ['required', 'string', 'max:25'],
            'postal_code' => ['required', 'string', 'max:25'],
            'npwp' => ['required', 'string', 'max:25'],
            'pers_responsible' => ['required', 'string', 'max:50'],
            'pos_pers_responsible' => ['required', 'string', 'max:50'],
            'mou_sign_name' => ['required', 'string', 'max:50'],
            'pos_sign_name' => ['required', 'string', 'max:50'],
            'administrator_name' => ['required', 'string', 'max:50'],
            'administrator_phone' => ['required', 'string', 'max:20'],
            'logo_big' => ['required', 'mimes:png', 'max:1000'],
            'logo_small' => ['required', 'mimes:png', 'max:1000'],
            'agreement' => ['required'],
            'noref' => ['required', 'string', 'exists:tcompany,noref'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(Request $request, string $client_id, string $logoBigName, string $logoSmallName)
    {
        $logs = new Logs(Arr::last(explode("\\", get_class())) . 'Log');
        $logs->write(__FUNCTION__, 'START');

        $ref = DB::table('tcompany as a')->sharedLock()
            ->where('a.noref', $request->noref)
            ->firstOrFail(['a.id']);

        if (! $request->file('logo_big')->storeAs('images/logo', $logoBigName, 'public')
            || ! $request->file('logo_small')->storeAs('images/logo', $logoSmallName, 'public')) {
            throw new \RuntimeException('Logo tidak dapat disimpan.');
        }

        $logs->write(__FUNCTION__, "STOP\r\n");

        return DB::table('tclient')
            ->insert([
                'client_id' => $client_id,
                'instansi_name' => $request->name_apps,
                'email' => $request->email,
                'application_name' => $request->name_apps,
                'address' => $request->address,
                'country_id' => $request->country,
                'provinsi_id' => $request->province,
                'kabupaten_id' => $request->regency,
                'kecamatan_id' => $request->district,
                'kelurahan_id' => $request->subdistrict,
                'kodepos' => $request->postal_code,
                'npwp' => $request->npwp,
                'pers_responsible' => $request->pers_responsible,
                'pos_pers_responsible' => $request->pos_pers_responsible,
                'mou_sign_name' => $request->mou_sign_name,
                'pos_sign_name' => $request->pos_sign_name,
                'administrator_name' => $request->administrator_name,
                'administrator_phone' => $request->administrator_phone,
                'logo' => $logoBigName,
                'logo_small' => $logoSmallName,
                'company_id' => $ref->id,
                'web_add' => $request->web_add,
                'agreement' => $request->agreement ? 'Y' : 'N',
                'created_at' => Carbon::now('Asia/Jakarta'),
                'updated_at' => Carbon::now('Asia/Jakarta')
            ]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }
}
