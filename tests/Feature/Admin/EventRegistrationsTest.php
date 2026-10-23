<?php

use App\Models\Event;
use App\Models\Registration;
use Inertia\Testing\AssertableInertia as Assert;

test('guest can view registrations in deterministic order with personal data', function () {
    $event = Event::factory()->create(['title' => 'Registration Review']);
    $later = Registration::factory()->for($event)->create([
        'name' => 'Later Attendee',
        'email' => 'later@example.com',
        'created_at' => '2027-03-01 10:00:00',
    ]);
    $earlierFirst = Registration::factory()->for($event)->create([
        'name' => 'Earlier First',
        'email' => 'first@example.com',
        'created_at' => '2027-03-01 09:00:00',
    ]);
    $earlierSecond = Registration::factory()->for($event)->create([
        'name' => 'Earlier Second',
        'email' => 'second@example.com',
        'created_at' => '2027-03-01 09:00:00',
    ]);

    $response = $this->get(route('admin.events.registrations.index', $event));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/events/registrations')
            ->where('event', [
                'id' => $event->id,
                'title' => 'Registration Review',
            ])
            ->where('timezone', 'UTC')
            ->has('registrations', 3)
            ->where('registrations.0', [
                'id' => $earlierFirst->id,
                'name' => 'Earlier First',
                'email' => 'first@example.com',
                'registeredAt' => '2027-03-01T09:00:00.000000Z',
            ])
            ->where('registrations.1.id', $earlierSecond->id)
            ->where('registrations.2.id', $later->id));
});

test('guest registrant view returns an empty collection for an event with no registrations', function () {
    $event = Event::factory()->create(['title' => 'Empty Event']);

    $response = $this->get(route('admin.events.registrations.index', $event));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/events/registrations')
            ->where('event', [
                'id' => $event->id,
                'title' => 'Empty Event',
            ])
            ->where('registrations', [])
            ->where('timezone', 'UTC'));
});

test('unknown event registrant view returns a normal not found response', function () {
    $response = $this->get(route('admin.events.registrations.index', 999999));

    $response->assertNotFound();
});
