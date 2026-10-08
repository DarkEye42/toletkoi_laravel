<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }

    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        // In production: send email to admin
        // Mail::to(setting('general.admin_email', 'admin@toletkoi.com'))
        //     ->send(new ContactFormSubmission($validated));

        return back()->with('success', 'Thank you for your message! We will get back to you within 24 hours.');
    }
}
