<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'auth_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'mobile',
        'otp',
        'otp_verified',
        'is_blocked',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'otp',
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
            'otp_verified' => 'boolean',
            'is_blocked' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the verified user details (KYC).
     */
    public function verifiedUser()
    {
        return $this->hasOne(VerifiedUser::class, 'auth_user_id');
    }

    /**
     * Alias for verifiedUser to stay compatible with existing code if needed
     */
    public function profile()
    {
        return $this->verifiedUser();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
