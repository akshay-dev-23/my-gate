<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    const OTP_EXPIRE_TIME_MINUTES = 10;
    protected $fillable = ['mobile_number', 'otp_code'];
}
