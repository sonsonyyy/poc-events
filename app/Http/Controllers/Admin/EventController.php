<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveEventRequest;
use App\Models\Event;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $events = Event::query()
            ->select(['id', 'title', 'starts_at', 'ends_at', 'location'])
            ->orderBy('starts_at')
            ->orderBy('id')
            ->get()
            ->map(fn (Event $event): array => $this->summary($event));

        return Inertia::render('admin/events/index', [
            'events' => $events,
            'timezone' => (string) config('app.timezone'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/events/create', [
            'timezone' => (string) config('app.timezone'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveEventRequest $request): RedirectResponse
    {
        $event = Event::query()->create($this->eventAttributes($request));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Event created.',
        ]);

        return to_route('admin.events.edit', $event);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event): Response
    {
        $timezone = (string) config('app.timezone');

        return Inertia::render('admin/events/edit', [
            'event' => [
                ...$this->summary($event),
                'description' => $event->description,
                'startsAtLocal' => $event->starts_at->setTimezone($timezone)->format('Y-m-d\TH:i'),
                'endsAtLocal' => $event->ends_at->setTimezone($timezone)->format('Y-m-d\TH:i'),
            ],
            'timezone' => $timezone,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveEventRequest $request, Event $event): RedirectResponse
    {
        $event->update($this->eventAttributes($request));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Event updated.',
        ]);

        return to_route('admin.events.edit', $event);
    }

    /**
     * @return array{title: string, description: string, location: string, starts_at: CarbonImmutable, ends_at: CarbonImmutable}
     */
    private function eventAttributes(SaveEventRequest $request): array
    {
        $timezone = (string) config('app.timezone');

        return [
            'title' => $request->string('title')->toString(),
            'description' => $request->string('description')->toString(),
            'location' => $request->string('location')->toString(),
            'starts_at' => CarbonImmutable::createFromFormat(
                'Y-m-d\TH:i',
                $request->string('starts_at')->toString(),
                $timezone,
            )->utc(),
            'ends_at' => CarbonImmutable::createFromFormat(
                'Y-m-d\TH:i',
                $request->string('ends_at')->toString(),
                $timezone,
            )->utc(),
        ];
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
