import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, ExternalLink, Users } from 'lucide-react';
import { update } from '@/actions/App/Http/Controllers/Admin/EventController';
import EventForm from '@/components/events/event-form';
import { Button } from '@/components/ui/button';
import { index } from '@/routes/admin/events';
import { index as registrationsIndex } from '@/routes/admin/events/registrations';
import { show } from '@/routes/events';
import type { EventEditData } from '@/types/events';

type EditEventProps = {
    event: EventEditData;
    timezone: string;
};

export default function EditEvent({ event, timezone }: EditEventProps) {
    return (
        <>
            <Head title={`Edit ${event.title}`} />

            <div className="grid gap-8">
                <Link
                    href={index()}
                    className="inline-flex w-fit items-center gap-2 rounded-sm text-sm font-medium text-muted-foreground hover:text-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                >
                    <ArrowLeft className="size-4" aria-hidden="true" />
                    Manage events
                </Link>

                <div className="grid gap-5 lg:grid-cols-[minmax(0,1fr)_15rem] lg:items-start">
                    <div className="grid gap-6 rounded-xl border bg-background p-5 shadow-sm sm:p-8">
                        <header className="grid gap-2">
                            <h1 className="text-3xl font-semibold tracking-tight">
                                Edit event
                            </h1>
                            <p className="text-sm leading-6 text-muted-foreground">
                                Update {event.title}. Existing registrations are
                                not changed.
                            </p>
                        </header>

                        <EventForm
                            form={update.form(event.id)}
                            timezone={timezone}
                            event={event}
                        />
                    </div>

                    <aside className="grid gap-2 rounded-xl border bg-background p-4">
                        <h2 className="text-sm font-semibold">Event links</h2>
                        <Button asChild variant="outline" size="sm">
                            <Link href={registrationsIndex(event.id)}>
                                <Users aria-hidden="true" />
                                Registrants
                            </Link>
                        </Button>
                        <Button asChild variant="ghost" size="sm">
                            <Link href={show(event.id)}>
                                <ExternalLink aria-hidden="true" />
                                Public page
                            </Link>
                        </Button>
                    </aside>
                </div>
            </div>
        </>
    );
}
