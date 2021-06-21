<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class EnsureLoginIsValid
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
        $extendRoute = [
            'logout',
            'forgot',
            'reset',
            'login'
        ];
        if (!in_array($request->route()->getName(), $extendRoute)) {
            $validLogin = auth()->user();

            if ($validLogin && $validLogin->id) {
                $currentTime = \Carbon\Carbon::now();
                $lastRequestTime = $validLogin->updated_at;
//                $diffTime = $currentTime->diffInHours($lastRequestTime);
                $diffTime = $currentTime->diffInMinutes($lastRequestTime);

                $admin = Admin::find($validLogin->id);

                // If time's up, clear value for Token, Ip column
                if ($diffTime >= 15) {
                    $admin->update(['token' => null, 'ip' => null]);
                    auth()->logout();
                } else {
                    // Else update latest request time
                    $token = auth()->tokenById($validLogin->id) ? auth()->tokenById($validLogin->id) : 'tmp-token';
                    $input = [
                        'token' => substr($token, 0, 100),
                        'ip' => $request->getClientIp(),
                        'updated_at' => \Carbon\Carbon::now()
                    ];
                    $admin->update($input);
                }
            }
        }
        return $next($request);
    }
}
