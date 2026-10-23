type EventDateTimeProps = {
    startsAt: string;
    endsAt: string;
    timezone: string;
};

const dateTimeFormatter = (timezone: string): Intl.DateTimeFormat =>
    new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
        timeZone: timezone,
    });

export function ZonedDateTime({
    dateTime,
    timezone,
}: {
    dateTime: string;
    timezone: string;
}) {
    return (
        <span className="inline-flex flex-wrap items-baseline gap-x-1.5 gap-y-1">
            <time dateTime={dateTime}>
                {dateTimeFormatter(timezone).format(new Date(dateTime))}
            </time>
            <span className="text-xs font-medium text-muted-foreground">
                ({timezone})
            </span>
        </span>
    );
}

export default function EventDateTime({
    startsAt,
    endsAt,
    timezone,
}: EventDateTimeProps) {
    const formatter = dateTimeFormatter(timezone);

    return (
        <span className="inline-flex flex-wrap items-baseline gap-x-1.5 gap-y-1">
            <time dateTime={startsAt}>
                {formatter.format(new Date(startsAt))}
            </time>
            <span aria-hidden="true">–</span>
            <span className="sr-only">to</span>
            <time dateTime={endsAt}>{formatter.format(new Date(endsAt))}</time>
            <span className="text-xs font-medium text-muted-foreground">
                ({timezone})
            </span>
        </span>
    );
}
