<?php
namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Cnfusers extends Authenticatable{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reseller_cnf';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'super_cnf_id',
        'name',
        'first_name',
        'last_name',
        'password',
        'email',
        'app_id',
        'date_of_birth',
        'gender',
        'addressLine1',
        'address',
        'country',
        'state',
        'district',
        'tehsil',
        'pincode',
        'police_station',
        'aadhar_number',
        'pan_number',
        'mobile_number',
        'alternate_mobile_number',
        'telephone_number',
        'gst_number',
        'ret_price',
        'dist_price',
        'super_dist_price',
        'cnf_type',
        'brandName',
        'displayName',
        'companyName',
        'url',
        'smsKeyword',
        'emailAddress',
        'logoUrl',
        'videosUrl',
        'service',
        'account_holder_name',
        'bank_name',
        'account_type',
        'account_number',
        'ifsc_code',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     * @return string
     */
    public function setPasswordAttribute($value){
        $this->attributes['password'] = bcrypt($value);
    }
}