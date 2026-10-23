<?php

namespace App\Http\Controllers;

use App\Actions\CreateRegistration;
use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class EventRegistrationController extends Controller
{
    public function store(
        StoreRegistrationRequest $request,
        Event $event,
        CreateRegistration $createRegistration,
    ): RedirectResponse {
        $registration = $createRegistration->handle($event, [
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
        ]);

        Inertia::flash('registrationConfirmation', [
            'eventTitle' => $event->title,
            'attendeeName' => $registration->name,
        ]);

        return to_route('events.show', $event);
    }
}
