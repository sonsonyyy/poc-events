import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Users } from 'lucide-react';
import { ZonedDateTime } from '@/components/events/event-date-time';
import { edit } from '@/routes/admin/events';
import type { AdminRegistration, EventSummary } from '@/types/events';

type RegistrationsProps = {
    event: Pick<EventSummary, 'id' | 'title'>;
    registrations: AdminRegistration[];
    timezone: string;
};

export default function Registrations({
    event,
    registrations,
    timezone,
}: RegistrationsProps) {
    return (
        <>
            <Head title={`${event.title} registrants`} />

            <div className="grid gap-8">
                <Link
                    href={edit(event.id)}
                    className="inline-flex w-fit items-center gap-2 rounded-sm text-sm font-medium text-muted-foreground hover:text-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                >
                    <ArrowLeft className="size-4" aria-hidden="true" />
                    Edit event
                </Link>

                <header className="grid max-w-3xl gap-2">
                    <p className="text-sm font-medium text-muted-foreground">
                        Registrant details
                    </p>
                    <h1 className="text-3xl font-semibold tracking-tight">
                        {event.title}
                    </h1>
                    <p className="text-sm leading-6 text-muted-foreground">
                        Names and email addresses are personal data. Use them
                        only for this event.
                    </p>
                </header>

                {registrations.length === 0 ? (
                    <section className="rounded-xl border border-dashed bg-background px-6 py-16 text-center">
                        <Users
                            className="mx-auto size-10 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <h2 className="mt-4 text-lg font-semibold">
                            No registrations yet
                        </h2>
                        <p className="mt-2 text-sm text-muted-foreground">
                            Attendees will appear here after they register.
                        </p>
                    </section>
                ) : (
                    <div className="overflow-hidden rounded-xl border bg-background shadow-sm">
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-2xl border-collapse text-left text-sm">
                                <caption className="sr-only">
                                    Registrants for {event.title}
                                </caption>
                                <thead className="border-b bg-muted/50">
                                    <tr>
                                        <th
                                            scope="col"
                                            className="px-4 py-3 font-medium"
                                        >
                                            Name
                                        </th>
                                        <th
                                            scope="col"
                                            className="px-4 py-3 font-medium"
                                        >
                                            Email
                                        </th>
                                        <th
                                            scope="col"
                                            className="px-4 py-3 font-medium"
                                        >
                                            Registered
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y">
                                    {registrations.map((registration) => (
                                        <tr key={registration.id}>
                                            <td className="px-4 py-3 font-medium">
                                                {registration.name}
                                            </td>
                                            <td className="px-4 py-3 break-all">
                                                {registration.email}
                                            </td>
                                            <td className="px-4 py-3 whitespace-nowrap text-muted-foreground">
                                                <ZonedDateTime
                                                    dateTime={
                                                        registration.registeredAt
                                                    }
                                                    timezone={timezone}
                                                />
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
