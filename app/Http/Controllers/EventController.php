<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(): Response
    {
        $events = Event::query()
            ->select(['id', 'title', 'starts_at', 'ends_at', 'location'])
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get()
            ->map(fn (Event $event): array => $this->summary($event));

        return Inertia::render('events/index', [
            'events' => $events,
            'timezone' => (string) config('app.timezone'),
        ]);
    }

    public function show(Event $event): Response
    {
        return Inertia::render('events/show', [
            'event' => [
                ...$this->summary($event),
                'description' => $event->description,
            ],
            'timezone' => (string) config('app.timezone'),
        ]);
    }

    /**
     * @return array{id: int, title: string, startsAt: string, endsAt: string, location: string}
     */
    private function summary(Event $event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'startsAt' => $event->starts_at->utc()->toISOString(),
            'endsAt' => $event->ends_at->utc()->toISOString(),
            'location' => $event->location,
        ];
    }
}
