<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait PartnerPasswordCredentials
{
    protected function partnerCredentials(Request $request, bool $reset = false): array
    {
        $credentials = $reset
            ? $request->only('email', 'password', 'password_confirmation', 'token')
            : $request->only('email');

        $credentials['partner'] = function (Builder $query) {
            $query->where('users.role', '!=', 0)->whereExists(function ($company) {
                $company->select(DB::raw('1'))
                    ->from('tcompany')
                    ->whereColumn('tcompany.id', 'users.client_id')
                    ->whereIn('tcompany.type', ['1', '2', '3']);
            });
        };

        return $credentials;
    }
}
