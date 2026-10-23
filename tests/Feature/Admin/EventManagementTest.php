<?php

use App\Models\Event;
use App\Models\Registration;
use Inertia\Testing\AssertableInertia as Assert;

test('guest can access every admin event page without a login redirect', function () {
    $event = Event::factory()->create();

    $this->get(route('admin.events.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/events/index'));
    $this->get(route('admin.events.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/events/create'));
    $this->get(route('admin.events.edit', $event))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/events/edit'));
});

test('admin event listing uses deterministic chronological order', function () {
    $later = Event::factory()->create([
        'starts_at' => '2027-06-01 09:00:00',
        'ends_at' => '2027-06-01 10:00:00',
    ]);
    $earlierFirst = Event::factory()->create([
        'starts_at' => '2027-05-01 09:00:00',
        'ends_at' => '2027-05-01 10:00:00',
    ]);
    $earlierSecond = Event::factory()->create([
        'starts_at' => '2027-05-01 09:00:00',
        'ends_at' => '2027-05-01 11:00:00',
    ]);

    $response = $this->get(route('admin.events.index'));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/events/index')
            ->where('events.0.id', $earlierFirst->id)
            ->where('events.1.id', $earlierSecond->id)
            ->where('events.2.id', $later->id)
            ->where('timezone', 'UTC'));
});

test('guest can create an event with local input persisted in UTC', function () {
    config(['app.timezone' => 'Asia/Manila']);

    $response = $this->post(route('admin.events.store'), [
        'title' => '  Manila Meetup  ',
        'description' => '  A focused meetup.  ',
        'location' => '  Makati  ',
        'starts_at' => '2027-01-15T09:30',
        'ends_at' => '2027-01-15T11:00',
    ]);

    $event = Event::query()->sole();

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.events.edit', $event))
        ->assertInertiaFlash('toast', [
            'type' => 'success',
            'message' => 'Event created.',
        ]);
    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Manila Meetup',
        'description' => 'A focused meetup.',
        'location' => 'Makati',
        'starts_at' => '2027-01-15 01:30:00',
        'ends_at' => '2027-01-15 03:00:00',
    ]);
    expect($event->starts_at->toISOString())->toBe('2027-01-15T01:30:00.000000Z');
});

test('event creation rejects missing malformed and non-increasing date values', function () {
    $missingResponse = $this->post(route('admin.events.store'), []);

    $missingResponse->assertSessionHasErrors([
        'title',
        'description',
        'location',
        'starts_at',
        'ends_at',
    ]);
    $this->assertDatabaseCount('events', 0);

    $invalidResponse = $this->post(route('admin.events.store'), [
        'title' => 'Invalid event',
        'description' => 'Description',
        'location' => 'Location',
        'starts_at' => '2027-02-20 10:00',
        'ends_at' => '2027-02-20T09:59',
    ]);

    $invalidResponse->assertSessionHasErrors(['starts_at', 'ends_at']);
    $this->assertDatabaseCount('events', 0);
});

test('guest can update an event without changing its registrations', function () {
    $event = Event::factory()->create([
        'title' => 'Original event',
        'starts_at' => '2027-02-20 10:00:00',
        'ends_at' => '2027-02-20 11:00:00',
    ]);
    $registration = Registration::factory()->for($event)->create();

    $response = $this->patch(route('admin.events.update', $event), [
        'title' => 'Updated event',
        'description' => 'Updated description',
        'location' => 'Updated location',
        'starts_at' => '2027-02-21T12:00',
        'ends_at' => '2027-02-21T14:00',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.events.edit', $event))
        ->assertInertiaFlash('toast', [
            'type' => 'success',
            'message' => 'Event updated.',
        ]);
    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Updated event',
        'starts_at' => '2027-02-21 12:00:00',
        'ends_at' => '2027-02-21 14:00:00',
    ]);
    $this->assertDatabaseHas('registrations', [
        'id' => $registration->id,
        'event_id' => $event->id,
        'email' => $registration->email,
    ]);
    $this->assertDatabaseCount('registrations', 1);
});

test('invalid event update leaves the event and registrations unchanged', function () {
    $event = Event::factory()->create([
        'title' => 'Original event',
        'description' => 'Original description',
        'location' => 'Original location',
        'starts_at' => '2027-02-20 10:00:00',
        'ends_at' => '2027-02-20 11:00:00',
    ]);
    $registration = Registration::factory()->for($event)->create();

    $response = $this
        ->from(route('admin.events.edit', $event))
        ->patch(route('admin.events.update', $event), [
            'title' => 'Changed title',
            'description' => 'Changed description',
            'location' => 'Changed location',
            'starts_at' => '2027-02-21T12:00',
            'ends_at' => '2027-02-21T12:00',
        ]);

    $response
        ->assertRedirect(route('admin.events.edit', $event))
        ->assertSessionHasErrors(['ends_at']);
    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Original event',
        'description' => 'Original description',
        'location' => 'Original location',
        'starts_at' => '2027-02-20 10:00:00',
        'ends_at' => '2027-02-20 11:00:00',
    ]);
    $this->assertDatabaseHas('registrations', ['id' => $registration->id]);
});
