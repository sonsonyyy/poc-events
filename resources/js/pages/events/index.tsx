import { Head, Link } from '@inertiajs/react';
import { ArrowRight, CalendarDays, MapPin } from 'lucide-react';
import EventDateTime from '@/components/events/event-date-time';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { show } from '@/routes/events';
import type { EventSummary } from '@/types/events';

type EventsIndexProps = {
    events: EventSummary[];
    timezone: string;
};

export default function EventsIndex({ events, timezone }: EventsIndexProps) {
    return (
        <>
            <Head title="Events" />

            <div className="grid gap-8">
                <header className="grid max-w-2xl gap-3">
                    <p className="text-sm font-medium text-muted-foreground">
                        Upcoming gatherings
                    </p>
                    <h1 className="text-3xl font-semibold tracking-tight sm:text-4xl">
                        Find your next event
                    </h1>
                    <p className="text-base leading-7 text-muted-foreground">
                        Browse the full schedule and reserve your place in a few
                        steps.
                    </p>
                </header>

                {events.length === 0 ? (
                    <section className="rounded-xl border border-dashed bg-background px-6 py-16 text-center">
                        <CalendarDays
                            className="mx-auto size-10 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <h2 className="mt-4 text-lg font-semibold">
                            No events scheduled
                        </h2>
                        <p className="mt-2 text-sm text-muted-foreground">
                            Please check back soon for new events.
                        </p>
                    </section>
                ) : (
                    <section
                        aria-label="Upcoming events"
                        className="grid gap-5 md:grid-cols-2"
                    >
                        {events.map((event) => (
                            <Card key={event.id} className="h-full">
                                <CardHeader>
                                    <CardTitle className="text-xl leading-snug">
                                        {event.title}
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="grid grow gap-4 text-sm">
                                    <div className="flex items-start gap-3">
                                        <CalendarDays
                                            className="mt-0.5 size-4 shrink-0 text-muted-foreground"
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
                                            className="mt-0.5 size-4 shrink-0 text-muted-foreground"
                                            aria-hidden="true"
                                        />
                                        <span>{event.location}</span>
                                    </div>
                                </CardContent>
                                <CardFooter>
                                    <Button asChild variant="outline">
                                        <Link href={show(event.id)}>
                                            View details
                                            <ArrowRight aria-hidden="true" />
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
