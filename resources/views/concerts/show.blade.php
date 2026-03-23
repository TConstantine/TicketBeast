@extends('layouts.master')

@section('body')
<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-lg mx-auto px-4">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
            <div class="p-8">
                {{-- Title --}}
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $concert->title }}</h1>
                    <span class="text-gray-600 font-medium truncate">{{ $concert->subtitle }}</span>
                </div>

                {{-- Date --}}
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium text-gray-800">{{ $concert->formatted_date }}</span>
                </div>

                {{-- Time --}}
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium text-gray-800">Doors at {{ $concert->formatted_start_time }}</span>
                </div>

                {{-- Price --}}
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium text-gray-800">${{ $concert->ticket_price_in_dollars }}</span>
                </div>

                {{-- Venue --}}
                <div class="flex items-start gap-3 mb-6">
                    <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div class="text-gray-700">
                        <p class="font-medium text-gray-900">{{ $concert->venue }}</p>
                        <p>{{ $concert->venue_address }}</p>
                        <p>{{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}</p>
                    </div>
                </div>

                {{-- Additional Info --}}
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-900">Additional Information</p>
                        <p class="text-gray-600">{{ $concert->additional_information }}</p>
                    </div>
                </div>
            </div>

            {{-- Checkout section --}}
            <div class="border-t border-gray-200">
                <div class="p-8">
                    <ticket-checkout
                        :concert-id="{{ $concert->id }}"
                        concert-title="{{ $concert->title }}"
                        :price="{{ $concert->ticket_price }}"
                    ></ticket-checkout>
                </div>
            </div>
        </div>

        <p class="text-center text-gray-500 font-medium mt-6">Powered by TicketBeast</p>
    </div>
</div>
@endsection

@push('beforeScripts')
<script src="https://checkout.stripe.com/checkout.js"></script>
@endpush