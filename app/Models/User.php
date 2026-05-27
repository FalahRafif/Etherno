<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'uuid',
    'name',
    'username',
    'email',
    'password',
    'role_id',
    'profile_image_attachment_id',
    'created_by',
    'updated_by',
    'deleted_at',
    'deleted_by',
    'delete_status',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
            'delete_status' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function profileImageAttachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'profile_image_attachment_id');
    }

    public function bookingsAsOperator(): HasMany
    {
        return $this->hasMany(Booking::class, 'operator_id');
    }

    public function bookingHistoriesAsOperator(): HasMany
    {
        return $this->hasMany(BookingHistory::class, 'operator_id');
    }
}
