<?php

use App\Models\Event;
use App\Models\Registration;
use Inertia\Testing\AssertableInertia as Assert;

test('public event listing returns every event in chronological order with an id tie breaker', function () {
    $later = Event::factory()->create([
        'title' => 'Later event',
        'starts_at' => '2027-03-01 09:00:00',
        'ends_at' => '2027-03-01 11:00:00',
        'location' => 'Room C',
    ]);
    $earlierFirst = Event::factory()->create([
        'title' => 'Earlier first',
        'starts_at' => '2027-02-01 09:00:00',
        'ends_at' => '2027-02-01 10:00:00',
        'location' => 'Room A',
    ]);
    $earlierSecond = Event::factory()->create([
        'title' => 'Earlier second',
        'starts_at' => '2027-02-01 09:00:00',
        'ends_at' => '2027-02-01 12:00:00',
        'location' => 'Room B',
    ]);

    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('events/index')
            ->where('timezone', 'UTC')
            ->has('events', 3)
            ->where('events.0.id', $earlierFirst->id)
            ->where('events.0.title', 'Earlier first')
            ->where('events.0.startsAt', '2027-02-01T09:00:00.000000Z')
            ->where('events.0.endsAt', '2027-02-01T10:00:00.000000Z')
            ->where('events.0.location', 'Room A')
            ->where('events.1.id', $earlierSecond->id)
            ->where('events.2.id', $later->id));
});

test('public event listing returns an empty collection when there are no events', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('events/index')
            ->where('events', [])
            ->where('timezone', 'UTC'));
});

test('public event details contain event information without registration personal data', function () {
    $event = Event::factory()->create([
        'title' => 'Laravel Live',
        'description' => "A practical conference.\nAll levels welcome.",
        'starts_at' => '2027-04-10 01:00:00',
        'ends_at' => '2027-04-10 03:00:00',
        'location' => 'Main Hall',
    ]);
    Registration::factory()->for($event)->create([
        'email' => 'private@example.com',
    ]);

    $response = $this->get(route('events.show', $event));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('events/show')
            ->where('event', [
                'id' => $event->id,
                'title' => 'Laravel Live',
                'startsAt' => '2027-04-10T01:00:00.000000Z',
                'endsAt' => '2027-04-10T03:00:00.000000Z',
                'location' => 'Main Hall',
                'description' => "A practical conference.\nAll levels welcome.",
            ])
            ->where('timezone', 'UTC')
            ->missing('registrations'));

    expect(json_encode($response->inertiaProps(), JSON_THROW_ON_ERROR))
        ->not->toContain('private@example.com');
});

test('unknown public events return a normal not found response', function () {
    $response = $this->get(route('events.show', 999999));

    $response->assertNotFound();
});
