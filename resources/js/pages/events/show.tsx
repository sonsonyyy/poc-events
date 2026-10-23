import { Form, Head, Link, usePage } from '@inertiajs/react';
import { ArrowLeft, CalendarDays, MapPin } from 'lucide-react';
import { store as storeRegistration } from '@/actions/App/Http/Controllers/EventRegistrationController';
import EventDateTime from '@/components/events/event-date-time';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { home } from '@/routes';
import type { EventDetail } from '@/types/events';

type EventShowProps = {
    event: EventDetail;
    timezone: string;
};

export default function EventShow({ event, timezone }: EventShowProps) {
    const confirmation = usePage().flash.registrationConfirmation;

    return (
        <>
            <Head title={event.title} />

            <div className="grid gap-8">
                <Link
                    href={home()}
                    className="inline-flex w-fit items-center gap-2 rounded-sm text-sm font-medium text-muted-foreground hover:text-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                >
                    <ArrowLeft className="size-4" aria-hidden="true" />
                    All events
                </Link>

                {confirmation && (
                    <div
                        role="status"
                        aria-live="polite"
                        className="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-950 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-100"
                    >
                        <strong>
                            You're registered, {confirmation.attendeeName}.
                        </strong>{' '}
                        Your place at {confirmation.eventTitle} is confirmed.
                    </div>
                )}

                <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_24rem] lg:items-start">
                    <article className="grid gap-6">
                        <header className="grid gap-4">
                            <h1 className="text-3xl font-semibold tracking-tight sm:text-4xl">
                                {event.title}
                            </h1>
                            <div className="grid gap-3 text-sm sm:text-base">
                                <div className="flex items-start gap-3">
                                    <CalendarDays
                                        className="mt-0.5 size-5 shrink-0 text-muted-foreground"
                                        aria-hidden="true"
                                    />
                                    <EventDateTime
                                        startsAt={event.startsAt}
                                        endsAt={event.endsAt}
                                        timezone={timezone}
                                    />
                                </div>
                                <div className="flex items-start gap-3">
                                    <MapPin
                                        className="mt-0.5 size-5 shrink-0 text-muted-foreground"
                                        aria-hidden="true"
                                    />
                                    <span>{event.location}</span>
                                </div>
                            </div>
                        </header>

                        <div className="text-base leading-7 whitespace-pre-line text-muted-foreground">
                            {event.description}
                        </div>
                    </article>

                    <Card>
                        <CardHeader>
                            <CardTitle>Reserve your place</CardTitle>
                            <CardDescription>
                                Enter your details to register for this event.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Form
                                {...storeRegistration.form(event.id)}
                                className="grid gap-5"
                            >
                                {({ errors, processing }) => (
                                    <>
                                        <div className="grid gap-2">
                                            <Label htmlFor="name">Name</Label>
                                            <Input
                                                id="name"
                                                name="name"
                                                required
                                                autoComplete="name"
                                                maxLength={255}
                                                aria-invalid={Boolean(
                                                    errors.name,
                                                )}
                                                aria-describedby={
                                                    errors.name
                                                        ? 'name-error'
                                                        : undefined
                                                }
                                            />
                                            <InputError
                                                id="name-error"
                                                message={errors.name}
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="email">
                                                Email address
                                            </Label>
                                            <Input
                                                id="email"
                                                name="email"
                                                type="email"
                                                required
                                                autoComplete="email"
                                                inputMode="email"
                                                maxLength={255}
                                                aria-invalid={Boolean(
                                                    errors.email,
                                                )}
                                                aria-describedby={
                                                    errors.email
                                                        ? 'email-error'
                                                        : undefined
                                                }
                                            />
                                            <InputError
                                                id="email-error"
                                                message={errors.email}
                                            />
                                        </div>

                                        <Button
                                            type="submit"
                                            disabled={processing}
                                            className="w-full"
                                        >
                                            {processing && <Spinner />}
                                            {processing
                                                ? 'Registering…'
                                                : 'Register for event'}
                                        </Button>
                                    </>
                                )}
                            </Form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}
