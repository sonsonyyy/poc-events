import { Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { store } from '@/actions/App/Http/Controllers/Admin/EventController';
import EventForm from '@/components/events/event-form';
import { index } from '@/routes/admin/events';

export default function CreateEvent({ timezone }: { timezone: string }) {
    return (
        <>
            <Head title="Create event" />

            <div className="grid gap-8">
                <Link
                    href={index()}
                    className="inline-flex w-fit items-center gap-2 rounded-sm text-sm font-medium text-muted-foreground hover:text-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                >
                    <ArrowLeft className="size-4" aria-hidden="true" />
                    Manage events
                </Link>

                <div className="grid max-w-3xl gap-6 rounded-xl border bg-background p-5 shadow-sm sm:p-8">
                    <header className="grid gap-2">
                        <h1 className="text-3xl font-semibold tracking-tight">
                            Create event
                        </h1>
                        <p className="text-sm leading-6 text-muted-foreground">
                            Add the event details attendees need before they
                            register.
                        </p>
                    </header>

                    <EventForm form={store.form()} timezone={timezone} />
                </div>
            </div>
        </>
    );
}
