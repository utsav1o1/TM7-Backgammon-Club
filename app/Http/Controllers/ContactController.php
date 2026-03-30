<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            Mail::to('admin@the-magnificent7.com')->send(new ContactMessage($validated));
            
            return Redirect::back()->with('success', 'Message sent successfully!');
        } catch (\Exception $e) {
            \Log::error('Contact form email failed: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $validated
            ]);
            
            return Redirect::back()->withErrors(['message' => 'Failed to send email. Please check the logs or contact support. Error: ' . $e->getMessage()]);
        }
    }
}
