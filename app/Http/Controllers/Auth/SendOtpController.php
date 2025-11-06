<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Models\Otp;
use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Http\RedirectResponse;

class SendOtpController extends Controller
{
    public function __invoke(SendOtpRequest $request): RedirectResponse
    {
        $email = $request->validated()['email'];

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => explode('@', $email)[0], 'password' => bcrypt(uniqid())]
        );

        $user = $user->fresh();

        Otp::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $user->notify(new OtpNotification($code));

        return back()->with([
            'otp_sent' => true,
            'email' => $email,
        ]);
    }
}
