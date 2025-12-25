<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function registration(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required',
                'email'    => 'required|email',
                'number'   => 'required',
                'password' => 'required',
                'accept_terms_text' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'type' => 'validation',
                'errors'  => $e->errors(),
                'message' => 'Please fix the errors below.',
            ], 200);
        }

        // Prevent duplicate email entries
        $exists = Entry::query()
            ->where('collection', 'customer')
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Email already registered.',
            ]);
        }

        // Create Statamic Entry
        Entry::make()
            ->collection('customer')
            ->data([
                'title'     => $request->name,
                'slug'      => Str::slug($request->name),
                'email'     => $request->email,
                'number'     => $request->number,
                'password'  => bcrypt($request->password),
                'accept_terms_text' => $request->accept_terms,
            ])
            ->save();

        return response()->json([
            'status' => true,
            'message' => 'Signup successful!',
        ]);
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'type' => 'validation',
                'errors'  => $e->errors(),
                'message' => 'Please fix the errors below.',
            ], 200);
        }

        // Find customer entry
        $author = Entry::query()
        ->where('collection', 'customer')
        ->where('email', $request->email)
        ->first();

        if (!$author) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password.'
            ]);
        }

        // Verify password
        if (!Hash::check($request->password, $author->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password.'
            ]);
        }

        // Set session (or your custom login)
        session(['author_logged_in' => true]);
        session(['author_id' => $author->id()]);
        session(['author_slug' => $author->slug()]);
        session(['author_email' => $author->get('email')]);
        session(['author_name' => $author->get('title')]);
        session(['author_city' => $author->get('city')]);
        session(['author_address' => $author->get('address')]);
        session(['author_state' => $author->get('state')]);
        session(['author_zip' => $author->get('zip_code')]);
        session(['author_notes' => $author->get('notes')]);

        return response()->json([
            'status'   => true,
            'message'  => 'Login successful!',
            'redirect' => url('/')  // change your redirect
        ]);
    }

    public function logout()
    {   
        // Forget all author-related session variables
        session()->forget(['author_logged_in', 'author_id', 'author_email', 'author_name','author_city','author_address','author_state','author_zip','author_notes']);

        // Redirect to login page with a logout success message
        return redirect('/sign-in')->with('success', 'You’ve been logged out. See you soon!');
    }
}
