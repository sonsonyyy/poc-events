<?php

use App\Models\Event;
use App\Models\Registration;

test('registration requires a name and a valid email without persisting invalid input', function () {
    $event = Event::factory()->create();

    $missingResponse = $this
        ->from(route('events.show', $event))
        ->post(route('events.registrations.store', $event), []);

    $missingResponse
        ->assertRedirect(route('events.show', $event))
        ->assertSessionHasErrors(['name', 'email']);
    $this->assertDatabaseCount('registrations', 0);

    $invalidEmailResponse = $this
        ->from(route('events.show', $event))
        ->post(route('events.registrations.store', $event), [
            'name' => 'Attendee Name',
            'email' => 'not-an-email',
        ]);

    $invalidEmailResponse
        ->assertRedirect(route('events.show', $event))
        ->assertSessionHasErrors(['email']);
    $this->assertDatabaseCount('registrations', 0);
});

test('valid registration stores normalized values and flashes a one-time confirmation', function () {
    $event = Event::factory()->create(['title' => 'PHP Philippines']);

    $response = $this->post(route('events.registrations.store', $event), [
        'name' => '  Alex Attendee  ',
        'email' => '  ALEX@Example.COM  ',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('events.show', $event))
        ->assertInertiaFlash('registrationConfirmation', [
            'eventTitle' => 'PHP Philippines',
            'attendeeName' => 'Alex Attendee',
        ]);
    $this->assertDatabaseHas('registrations', [
        'event_id' => $event->id,
        'name' => 'Alex Attendee',
        'email' => 'alex@example.com',
    ]);
    $this->assertDatabaseCount('registrations', 1);

    $confirmationResponse = $this->get(route('events.show', $event));

    $confirmationResponse
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->hasFlash('registrationConfirmation', [
                'eventTitle' => 'PHP Philippines',
                'attendeeName' => 'Alex Attendee',
            ]));

    $this->get(route('events.show', $event))
        ->assertInertia(fn ($page) => $page->missingFlash('registrationConfirmation'));
});

test('same event duplicate registration is rejected after email normalization', function () {
    $event = Event::factory()->create();
    Registration::factory()->for($event)->create([
        'email' => 'person@example.com',
    ]);

    $response = $this
        ->from(route('events.show', $event))
        ->post(route('events.registrations.store', $event), [
            'name' => 'Duplicate Person',
            'email' => '  PERSON@Example.COM ',
        ]);

    $response
        ->assertRedirect(route('events.show', $event))
        ->assertSessionHasErrors([
            'email' => 'This email is already registered for this event.',
        ]);
    $this->assertDatabaseCount('registrations', 1);
    $this->assertDatabaseMissing('registrations', [
        'name' => 'Duplicate Person',
    ]);
});

test('same normalized email can register once for each event', function () {
    $firstEvent = Event::factory()->create();
    $secondEvent = Event::factory()->create();
    Registration::factory()->for($firstEvent)->create([
        'email' => 'shared@example.com',
    ]);

    $response = $this->post(route('events.registrations.store', $secondEvent), [
        'name' => 'Shared Attendee',
        'email' => ' SHARED@Example.com ',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('events.show', $secondEvent));
    $this->assertDatabaseCount('registrations', 2);
    $this->assertDatabaseHas('registrations', [
        'event_id' => $secondEvent->id,
        'email' => 'shared@example.com',
    ]);
});

test('registration against an unknown event returns a normal not found response', function () {
    $response = $this->post(route('events.registrations.store', 999999), [
        'name' => 'Unknown Event Attendee',
        'email' => 'unknown@example.com',
    ]);

    $response->assertNotFound();
    $this->assertDatabaseCount('registrations', 0);
});

test('all configured sql connections mask bindings in exception messages', function () {
    foreach (['sqlite', 'mysql', 'mariadb', 'pgsql', 'sqlsrv'] as $connection) {
        expect(config("database.connections.{$connection}.mask_bindings_in_exception_messages"))
            ->toBeTrue();
    }
});
