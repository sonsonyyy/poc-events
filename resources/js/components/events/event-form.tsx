import { Form } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import type { EventEditData } from '@/types/events';

type EventFormProps = {
    form: {
        action: string;
        method: 'post';
    };
    timezone: string;
    event?: EventEditData;
};

const inputClassName = 'aria-invalid:border-destructive';

export default function EventForm({ form, timezone, event }: EventFormProps) {
    const isEditing = event !== undefined;

    return (
        <Form {...form} className="grid gap-6">
            {({ errors, processing }) => (
                <>
                    <div className="grid gap-2">
                        <Label htmlFor="title">Title</Label>
                        <Input
                            id="title"
                            name="title"
                            defaultValue={event?.title}
                            required
                            autoFocus
                            maxLength={255}
                            aria-invalid={Boolean(errors.title)}
                            aria-describedby={
                                errors.title ? 'title-error' : undefined
                            }
                            className={inputClassName}
                        />
                        <InputError id="title-error" message={errors.title} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="description">Description</Label>
                        <textarea
                            id="description"
                            name="description"
                            defaultValue={event?.description}
                            required
                            rows={6}
                            aria-invalid={Boolean(errors.description)}
                            aria-describedby={
                                errors.description
                                    ? 'description-error'
                                    : undefined
                            }
                            className="min-h-32 w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:aria-invalid:ring-destructive/40"
                        />
                        <InputError
                            id="description-error"
                            message={errors.description}
                        />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="location">Location</Label>
                        <Input
                            id="location"
                            name="location"
                            defaultValue={event?.location}
                            required
                            maxLength={255}
                            aria-invalid={Boolean(errors.location)}
                            aria-describedby={
                                errors.location ? 'location-error' : undefined
                            }
                            className={inputClassName}
                        />
                        <InputError
                            id="location-error"
                            message={errors.location}
                        />
                    </div>

                    <fieldset className="grid gap-4 rounded-lg border p-4 sm:grid-cols-2">
                        <legend className="px-1 text-sm font-medium">
                            Schedule ({timezone})
                        </legend>

                        <div className="grid gap-2">
                            <Label htmlFor="starts_at">Starts at</Label>
                            <Input
                                id="starts_at"
                                name="starts_at"
                                type="datetime-local"
                                defaultValue={event?.startsAtLocal}
                                required
                                aria-invalid={Boolean(errors.starts_at)}
                                aria-describedby={
                                    errors.starts_at
                                        ? 'starts-at-error'
                                        : undefined
                                }
                                className={inputClassName}
                            />
                            <InputError
                                id="starts-at-error"
                                message={errors.starts_at}
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="ends_at">Ends at</Label>
                            <Input
                                id="ends_at"
                                name="ends_at"
                                type="datetime-local"
                                defaultValue={event?.endsAtLocal}
                                required
                                aria-invalid={Boolean(errors.ends_at)}
                                aria-describedby={
                                    errors.ends_at ? 'ends-at-error' : undefined
                                }
                                className={inputClassName}
                            />
                            <InputError
                                id="ends-at-error"
                                message={errors.ends_at}
                            />
                        </div>
                    </fieldset>

                    <Button
                        type="submit"
                        disabled={processing}
                        className="w-full sm:w-fit"
                    >
                        {processing && <Spinner />}
                        {processing
                            ? isEditing
                                ? 'Saving changes…'
                                : 'Creating event…'
                            : isEditing
                              ? 'Save changes'
                              : 'Create event'}
                    </Button>
                </>
            )}
        </Form>
    );
}
