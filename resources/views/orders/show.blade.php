@extends('layouts.master')

@section('body')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <div class="flex items-baseline justify-between py-4 border-b border-gray-200">
                    <h1 class="text-2xl">Order Summary</h1>
                    <a href="#" class="text-sky-500 hover:underline">{{ $order->confirmation_number }}</a>
                </div>
                <div class="py-4 border-b border-gray-200">
                    <p>
                        <strong>Order Total: ${{ number_format($order->amount / 100, 2) }}</strong>
                    </p>
                    <p class="text-gray-500">Billed to Card #: **** **** **** {{ $order->card_last_four }}</p>
                </div>
            </div>
            <div class="mb-12">
                <h2 class="text-xl font-normal mb-4">Your Tickets</h2>

                @foreach($order->tickets as $ticket)
                <div class="bg-white border border-gray-200 rounded overflow-hidden mb-6">
                    <div class="px-6 py-3 flex items-baseline justify-between text-white bg-gray-600">
                        <div>
                            <h1 class="text-2xl font-normal">{{ $ticket->concert->title }}</h1>
                            <p class="text-white/60">{{ $ticket->concert->subtitle }}</p>
                        </div>
                        <div class="text-right">
                            <strong>General Admission</strong>
                            <p class="text-white/90">Admit one</p>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-wrap -mx-4">
                            <div class="w-full sm:flex-1 px-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        @icon('calendar', 'text-sky-400')
                                    </div>
                                    <div class="flex-1 pl-4">
                                        <p class="font-bold">
                                            <time datetime="{{ $ticket->concert->date->format('Y-m-d H:i') }}">
                                                {{ $ticket->concert->date->format('l, F jS, Y') }}
                                            </time>
                                        </p>
                                        <p class="text-gray-500">Doors at {{ $ticket->concert->date->format('g:ia') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full sm:flex-1 px-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        @icon('location', 'text-sky-400')
                                    </div>
                                    <div class="flex-1 pl-4">
                                        <p class="font-bold">{{ $ticket->concert->venue }}</p>
                                        <div class="text-gray-500">
                                            <p>{{ $ticket->concert->venue_address }}</p>
                                            <p>{{ $ticket->concert->city }}, {{ $ticket->concert->state }} {{ $ticket->concert->zip }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 flex items-baseline justify-between">
                        <p class="text-xl">{{ $ticket->code }}</p>
                        <p>{{ $order->email }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center text-gray-500 font-semibold">
                <p>Powered by TicketBeast</p>
            </div>
        </div>
    </div>
</div>
@endsection