<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gst_number',
        'pan_number',
        'pan_image_path',
        'aadhaar_number',
        'aadhaar_front_image_path',
        'aadhaar_back_image_path',
        'selfie_image_path',
        'selfie_reference',
        'kyc_status',
        'kyc_submitted_at',
        'kyc_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kyc_submitted_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
