<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;

class Recipienter extends Authenticatable implements JWTSubject {
    use Notifiable;
    use Billable;

    protected $fillable = [
                            'email',
                            'password',
                            'name',
                            'phone',
                            'stamp',
                            'bank_code',
                            'branch_code',
                            'account_type',
                            'account_number',
                            'account_name',
                            'parent_id',
                            'type',
                            'status',
                            'is_email_authenticated',
                            'token',
                            'token_at',
                            'address',
                            'zipcode',
                            'prefecture',
                            'city',
                            'tanto_name',
                            'tenantId',
                            'tenant_id'
                        ];
    protected $primaryKey = 'id';
    protected $table = 'recipienters';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'stamp_url',
    ];

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getStampUrlAttribute()
    {
        if (is_null($this->stamp)) {
            return null;
        } else {
            return Storage::disk('public')->url($this->stamp);
        }
    }

    public function childUsers()
    {
        return $this->hasMany('\App\Models\Recipienter', 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo('\App\Models\Recipienter', 'parent_id', 'id');
    }

    public function users()
    {
        return $this->hasMany('\App\User', 'recipienter_id');
    }

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        if ($this->role_id == 1) {
            $this->notify(new VerifyEmail);
        }
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}