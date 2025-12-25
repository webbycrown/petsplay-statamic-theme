<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Statamic\Facades\Parse;

class PasswordResetController extends Controller
{
    // Send reset link to user
    public function sendResetLink(Request $request)
    {
        try {
            $validated = $request->validate([
                'email'    => 'required|email',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'type' => 'validation',
                'errors'  => $e->errors(),
                'message' => 'Please fix the errors below.',
            ], 200);
        }

        $token = Str::random(64);

        $user = Entry::query()
            ->where('collection', 'customer')
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email not registered.',
            ]);
        }

        $user->set('token', $token);
        $user->save();


        $resetLink = url('/reset-password/' . $token);

        $html = (string) Parse::template(
    file_get_contents(resource_path('views/emails/forgetPassword.antlers.html')),
    ['url' => $resetLink]
);

        Mail::html($html, function ($message) use ($request) {

          $message->to($request->email);

          $message->subject('Reset Password');

      });

        return response()->json([
            'status' => true,
            'message' => 'We have e-mailed your password reset link!'
        ]);

    }


    public function reset(Request $request)
    {
        try {
            $validated = $request->validate([                
                'password' => 'required|min:8|confirmed',
                'password_confirmation' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'type' => 'validation',
                'errors'  => $e->errors(),
                'message' => 'Please fix the errors below.',
            ], 200);
        }

        if( $request->token == '' ){
            return response()->json(['status' => false ,'message' => 'Invalid token']);
        }

        // token check
        $user = Entry::query()
            ->where('collection', 'customer')
            ->where('token', $request->token)
            ->first();

        if (!$user) {
            return response()->json(['status' => false ,'message' => 'Invalid token']);
        }


        // update password
        $password = Hash::make($request->password);
        $user->set('password', $password);
        $user->set('token', '');
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully.'
        ]);
    }
}
