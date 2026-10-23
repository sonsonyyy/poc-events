export type EventSummary = {
    id: number;
    title: string;
    startsAt: string;
    endsAt: string;
    location: string;
};

export type EventDetail = EventSummary & {
    description: string;
};

export type EventEditData = EventDetail & {
    startsAtLocal: string;
    endsAtLocal: string;
};

export type AdminRegistration = {
    id: number;
    name: string;
    email: string;
    registeredAt: string;
};

export type RegistrationConfirmation = {
    eventTitle: string;
    attendeeName: string;
};
