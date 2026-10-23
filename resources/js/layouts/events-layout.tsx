import { Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { index as adminEventsIndex } from '@/routes/admin/events';
import { home } from '@/routes';

const navigationClassName =
    'rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50';

export default function EventsLayout({ children }: { children: ReactNode }) {
    const { component } = usePage();
    const isAdmin = component.startsWith('admin/events/');

    return (
        <div className="min-h-screen bg-muted/30 text-foreground">
            <header className="border-b bg-background">
                <div className="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                    <Link
                        href={home()}
                        className="w-fit rounded-sm text-lg font-semibold tracking-tight focus-visible:ring-[3px] focus-visible:ring-ring/50 focus-visible:outline-none"
                    >
                        Eventful
                    </Link>

                    <nav
                        aria-label="Primary navigation"
                        className="flex flex-wrap items-center gap-1"
                    >
                        <Link
                            href={home()}
                            aria-current={!isAdmin ? 'page' : undefined}
                            className={`${navigationClassName} ${
                                !isAdmin
                                    ? 'bg-accent text-accent-foreground'
                                    : ''
                            }`}
                        >
                            Browse events
                        </Link>
                        <Link
                            href={adminEventsIndex()}
                            aria-current={isAdmin ? 'page' : undefined}
                            className={`${navigationClassName} ${
                                isAdmin
                                    ? 'bg-accent text-accent-foreground'
                                    : ''
                            }`}
                        >
                            Event admin
                        </Link>
                    </nav>
                </div>
            </header>

            {isAdmin && (
                <div className="border-b border-amber-300 bg-amber-50 text-amber-950 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-100">
                    <div className="mx-auto w-full max-w-6xl px-4 py-3 text-sm sm:px-6 lg:px-8">
                        <strong>POC notice:</strong> This administration area is
                        intentionally unauthenticated. Anyone with access can
                        change events and view registrant details.
                    </div>
                </div>
            )}

            <main className="mx-auto w-full max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
                {children}
            </main>
        </div>
    );
}
