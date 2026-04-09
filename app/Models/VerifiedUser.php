<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifiedUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'auth_user_id',
        'full_name',
        'email',
        'pan_number',
        'aadhaar_number',
        'pan_image',
        'aadhaar_image',
        'selfie_image',
        'kyc_status',
        'gold_limit',
        'silver_limit',
        'is_trading_enabled',
    ];

    /**
     * Get the auth user that owns the verified details.
     */
    public function authUser()
    {
        return $this->belongsTo(User::class, 'auth_user_id');
    }

    /**
     * Alias for authUser to support short syntax in admin views.
     */
    public function user()
    {
        return $this->authUser();
    }

    /**
     * Helper to check if KYC is approved.
     */
    public function isVerified(): bool
    {
        return $this->kyc_status === 'approved';
    }
}
