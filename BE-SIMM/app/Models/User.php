<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;
    protected $keyType = 'uuid';

    public static function boot(){
        parent::boot();

        static::created(function ($model){
            $model->OtpCode();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'id_role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function generateOtpCode(){
        //mengecek apakah otp code sudah terdaftar di DB
        do {
            $randomNumber = mt_rand(100000, 999999);
            $checkOtpCode = OtpCode::where('otp', $randomNumber)->first();
        } while ($checkOtpCode);

        $now = Carbon::now();

        $otp_code = OtpCode::UpdateOrCreate(
            ['id_user' => $this->id],
            [
                'otp' => $randomNumber,
                'valid_until' => $now->addMinutes(5)
            ]
        );
    }
    
    public function otpcode()
    {
        return $this->hasOne(OtpCode::class, 'id_user');
    }

    public function Role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function kepala_bagian()
    {
        return $this->hasOne(KepalaBagian::class, 'id_user');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_user');
    }
    
    public function peserta()
    {
        return $this->hasOne(Peserta::class, 'id_user');
    }

}
