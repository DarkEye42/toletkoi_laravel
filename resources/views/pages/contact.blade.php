@extends('layouts.app')

@section('title', 'Contact Us - ToLetKoi')
@section('description', 'Get in touch with the ToLetKoi team.')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900">Contact Us</h1>
        <p class="mt-4 text-lg text-gray-600">Have a question or feedback? We'd love to hear from you.</p>
    </div>

    <div class="mt-12 grid gap-8 sm:grid-cols-2">
        <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Send us a Message</h2>
            <form action="{{ route('contact.store') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                    <input type="text" id="subject" name="subject" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea id="message" name="message" rows="4" required
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>
                <button type="submit"
                        class="w-full rounded-lg bg-emerald-600 px-6 py-3 text-base font-semibold text-white hover:bg-emerald-700 transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    Send Message
                </button>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">📍 Office Address</h3>
                <p class="mt-3 text-gray-600">Dhanmondi, Dhaka 1205<br>Bangladesh</p>
            </div>
            <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">📧 Email</h3>
                <p class="mt-3 text-gray-600">support@toletkoi.com<br>info@toletkoi.com</p>
            </div>
            <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">📱 Phone</h3>
                <p class="mt-3 text-gray-600">+880 1700-000000<br>Sun-Thu: 9 AM - 6 PM</p>
            </div>
        </div>
    </div>
</div>
@endsection
