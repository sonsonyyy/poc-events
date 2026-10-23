<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Carbon\CarbonInterface;
use Inertia\Inertia;
use Inertia\Response;
use LogicException;

class EventRegistrationController extends Controller
{
    public function index(Event $event): Response
    {
        $registrations = $event->registrations()
            ->select(['id', 'event_id', 'name', 'email', 'created_at'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->map(function (Registration $registration): array {
                $registeredAt = $registration->created_at;

                if (! $registeredAt instanceof CarbonInterface) {
                    throw new LogicException('Persisted registrations must have a creation timestamp.');
                }

                return [
                    'id' => $registration->id,
                    'name' => $registration->name,
                    'email' => $registration->email,
                    'registeredAt' => $registeredAt->utc()->toISOString(),
                ];
            });

        return Inertia::render('admin/events/registrations', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
            ],
            'registrations' => $registrations,
            'timezone' => (string) config('app.timezone'),
        ]);
    }
}
