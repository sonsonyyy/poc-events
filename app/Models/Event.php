<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Database\Factories\EventFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $location
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable $ends_at
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 */
#[Fillable(['title', 'description', 'location', 'starts_at', 'ends_at'])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    /**
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * @return Attribute<CarbonImmutable, DateTimeInterface|string>
     */
    protected function startsAt(): Attribute
    {
        return $this->utcDateTimeAttribute();
    }

    /**
     * @return Attribute<CarbonImmutable, DateTimeInterface|string>
     */
    protected function endsAt(): Attribute
    {
        return $this->utcDateTimeAttribute();
    }

    /**
     * @return Attribute<CarbonImmutable, DateTimeInterface|string>
     */
    private function utcDateTimeAttribute(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): CarbonImmutable => CarbonImmutable::parse($value, 'UTC'),
            set: fn (DateTimeInterface|string $value): string => CarbonImmutable::parse(
                $value,
                is_string($value) ? config('app.timezone') : null,
            )->utc()->format($this->getDateFormat()),
        );
    }
}
