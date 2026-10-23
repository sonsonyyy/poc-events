<?php

use App\Actions\CreateRegistration;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

test('database schema protects registration uniqueness and event ownership', function () {
    $indexes = collect(Schema::getIndexes('registrations'));
    $foreignKeys = collect(Schema::getForeignKeys('registrations'));

    expect($indexes->firstWhere('name', 'registrations_event_id_email_unique'))
        ->not->toBeNull()
        ->and($indexes->firstWhere('name', 'registrations_event_id_email_unique')['unique'])
        ->toBeTrue()
        ->and($indexes->firstWhere('name', 'registrations_event_id_email_unique')['columns'])
        ->toBe(['event_id', 'email'])
        ->and($indexes->firstWhere('name', 'registrations_event_id_created_at_id_index')['columns'])
        ->toBe(['event_id', 'created_at', 'id'])
        ->and($foreignKeys->firstWhere('foreign_table', 'events')['columns'])
        ->toBe(['event_id'])
        ->and($foreignKeys->firstWhere('foreign_table', 'events')['on_delete'])
        ->toBe('restrict');
});

test('database unique constraint permits at most one same-event email', function () {
    $event = Event::factory()->create();
    Registration::factory()->for($event)->create([
        'email' => 'unique@example.com',
    ]);

    try {
        DB::table('registrations')->insert([
            'event_id' => $event->id,
            'name' => 'Competing Attendee',
            'email' => 'unique@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->fail('The database should have rejected the competing insert.');
    } catch (UniqueConstraintViolationException $exception) {
        expect($exception->getMessage())->not->toContain('unique@example.com');
    }

    $this->assertDatabaseCount('registrations', 1);
});

test('event deletion is restricted while registrations exist', function () {
    $event = Event::factory()->create();
    Registration::factory()->for($event)->create();

    expect(fn () => $event->delete())->toThrow(QueryException::class);

    $this->assertDatabaseHas('events', ['id' => $event->id]);
    $this->assertDatabaseHas('registrations', ['event_id' => $event->id]);
});

test('competing registration insert becomes a field validation error', function () {
    $event = Event::factory()->create();
    Registration::factory()->for($event)->create([
        'email' => 'race@example.com',
    ]);

    try {
        app(CreateRegistration::class)->handle($event, [
            'name' => 'Losing Attendee',
            'email' => 'race@example.com',
        ]);

        $this->fail('A competing insert should have failed validation.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toBe([
            'email' => ['This email is already registered for this event.'],
        ]);
    }

    $this->assertDatabaseCount('registrations', 1);
});
