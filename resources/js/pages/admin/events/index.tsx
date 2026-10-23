import { Head, Link } from '@inertiajs/react';
import { CalendarDays, ExternalLink, Plus, Users } from 'lucide-react';
import EventDateTime from '@/components/events/event-date-time';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { create, edit } from '@/routes/admin/events';
import { index as registrationsIndex } from '@/routes/admin/events/registrations';
import { show } from '@/routes/events';
import type { EventSummary } from '@/types/events';

type AdminEventsIndexProps = {
    events: EventSummary[];
    timezone: string;
};

export default function AdminEventsIndex({
    events,
    timezone,
}: AdminEventsIndexProps) {
    return (
        <>
            <Head title="Event admin" />

            <div className="grid gap-8">
                <header className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div className="grid max-w-2xl gap-2">
                        <p className="text-sm font-medium text-muted-foreground">
                            Event administration
                        </p>
                        <h1 className="text-3xl font-semibold tracking-tight">
                            Manage events
                        </h1>
                        <p className="text-sm leading-6 text-muted-foreground">
                            Create events, update event details, and review
                            registrations.
                        </p>
                    </div>
                    <Button asChild>
                        <Link href={create()}>
                            <Plus aria-hidden="true" />
                            Create event
                        </Link>
                    </Button>
                </header>

                {events.length === 0 ? (
                    <section className="rounded-xl border border-dashed bg-background px-6 py-16 text-center">
                        <CalendarDays
                            className="mx-auto size-10 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <h2 className="mt-4 text-lg font-semibold">
                            No events yet
                        </h2>
                        <p className="mt-2 text-sm text-muted-foreground">
                            Create the first event to make it available to
                            attendees.
                        </p>
                        <Button asChild className="mt-5">
                            <Link href={create()}>Create event</Link>
                        </Button>
                    </section>
                ) : (
                    <section
                        aria-label="Managed events"
                        className="grid gap-5 lg:grid-cols-2"
                    >
                        {events.map((event) => (
                            <Card key={event.id} className="h-full">
                                <CardHeader>
                                    <CardTitle className="text-xl leading-snug">
                                        <Link
                                            href={edit(event.id)}
                                            className="rounded-sm hover:underline focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                                        >
                                            {event.title}
                                        </Link>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="grid grow gap-3 text-sm">
                                    <EventDateTime
                                        startsAt={event.startsAt}
                                        endsAt={event.endsAt}
                                        timezone={timezone}
                                    />
                                    <p>{event.location}</p>
                                </CardContent>
                                <CardFooter className="flex-wrap gap-2">
                                    <Button asChild size="sm">
                                        <Link href={edit(event.id)}>Edit</Link>
                                    </Button>
                                    <Button asChild size="sm" variant="outline">
                                        <Link
                                            href={registrationsIndex(event.id)}
                                        >
                                            <Users aria-hidden="true" />
                                            Registrants
                                        </Link>
                                    </Button>
                                    <Button asChild size="sm" variant="ghost">
                                        <Link href={show(event.id)}>
                                            <ExternalLink aria-hidden="true" />
                                            Public page
                                        </Link>
                                    </Button>
                                </CardFooter>
                            </Card>
                        ))}
                    </section>
                )}
            </div>
        </>
    );
}
