<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Mail\VoterOtpMail;
use App\Models\OtpToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found']);
        }

        if (!Auth::attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid email or password']);
        }

        /** @var User $user */
        $user = User::findOrFail(Auth::id());

        // Voters require OTP — log them back out until OTP is verified
        if ($user->hasRole('voter')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $otp = OtpToken::generate($user->id);
            Mail::to($user->getMailAddress())->send(new VoterOtpMail($otp->token, $user->name));

            // Store pending user id in session (not authenticated yet)
            session(['otp_pending_user_id' => $user->id]);

            return response()->json([
                'status'   => 'otp_required',
                'redirect' => route('voter.otp'),
            ]);
        }

        $redirect = match(true) {
            $user->hasRole('election_admin') => route('election.dashboard'),
            default                          => route('dashboard'),
        };

        return response()->json(['status' => 'success', 'redirect' => $redirect]);
    }

    // ── OTP page (GET) ────────────────────────────────────────────────────────
    public function otpForm()
    {
        if (!session('otp_pending_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.otp');
    }

    // ── OTP verification (POST) ───────────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $userId = session('otp_pending_user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'Session expired. Please log in again.']);
        }

        $otpRecord = OtpToken::where('user_id', $userId)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return response()->json(['status' => 'error', 'message' => 'OTP expired. Please log in again to receive a new one.']);
        }

        if ($otpRecord->token !== $request->otp) {
            return response()->json(['status' => 'error', 'message' => 'Incorrect OTP. Please try again.']);
        }

        $otpRecord->update(['used_at' => now()]);
        session()->forget('otp_pending_user_id');

        Auth::loginUsingId($userId);

        return response()->json(['status' => 'success', 'redirect' => route('voter.dashboard')]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function resetPassword(Request $request)
    {
        try {
            $validated = $request->validate(['email' => 'required|email']);

            $user = User::where('email', $validated['email'])->first();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Email not found']);
            }

            $token = rand(100000, 999999);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $validated['email']],
                ['token' => $token, 'created_at' => now()]
            );

            Mail::to($validated['email'])->send(new ResetPassword((object)[
                'email' => $validated['email'],
                'token' => $token,
            ]));

            return response()->json(['status' => 'success', 'message' => 'Check your email']);
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function changePassword(Request $request)
    {
        if (!$request->password) {
            $request->validate(['token' => 'required|string']);

            $tokenRecord = DB::table('password_reset_tokens')->where('token', $request->token)->first();
            if (!$tokenRecord) {
                return response()->json(['status' => 'error', 'message' => 'Invalid or expired token']);
            }
            return response()->json(['status' => 'token_valid']);
        }

        $request->validate(['password' => 'required|min:6|confirmed']);

        $tokenRecord = DB::table('password_reset_tokens')->where('token', $request->token)->first();
        if (!$tokenRecord) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired token']);
        }

        User::where('email', $tokenRecord->email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        return response()->json(['status' => 'password_reset', 'message' => 'Password successfully updated']);
    }
}
