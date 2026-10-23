<?php

namespace App\Actions;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;

class CreateRegistration
{
    /**
     * @param  array{name: string, email: string}  $attributes
     */
    public function handle(Event $event, array $attributes): Registration
    {
        try {
            return $event->registrations()->create($attributes);
        } catch (UniqueConstraintViolationException $exception) {
            if (! $this->isRegistrationEmailConflict($exception)) {
                throw $exception;
            }

            throw ValidationException::withMessages([
                'email' => 'This email is already registered for this event.',
            ]);
        }
    }

    private function isRegistrationEmailConflict(
        UniqueConstraintViolationException $exception,
    ): bool {
        return $exception->index === 'registrations_event_id_email_unique'
            || $exception->columns === ['event_id', 'email'];
    }
}
