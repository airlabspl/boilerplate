<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyOtpController extends Controller
{
    public function __invoke(VerifyOtpRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $email = $validated['email'];
        $code = $validated['otp'];

        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->withErrors([
                'otp' => 'Invalid or expired code.',
            ]);
        }

        $otp = Otp::where('user_id', $user->id)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            return back()->withErrors([
                'otp' => 'Invalid or expired code.',
            ]);
        }

        $otp->delete();

        Auth::login($user);

        $user = User::find($user->id);

        if ($user->hasEnabledTwoFactorAuthentication()) {
            return redirect('/two-factor-challenge');
        }

        return redirect()->route('dashboard');
    }
}
