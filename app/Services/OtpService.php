<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Otp;
use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class OtpService
{
    public function generateOTP()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $otp = '';
        for ($i = 0; $i < 6; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $otp;
    }

    public function sendOTP($mobile_number, $otp)
    {
        return true;
        // Your OTP sending logic here
    }

    public function storeOTP($mobile_number, $otp)
    {
        Otp::create(['mobile_number' => $mobile_number, 'otp_code' => $otp]);
    }

    public function verifyOTP($mobile_number, $otp)
    {
        $otp = Otp::where(['mobile_number' => $mobile_number, 'otp_code' => $otp])->first();
        if (!$otp) throw new Exception('Invalid OTP.', Response::HTTP_BAD_REQUEST);
        $otp->delete();
        if ($this->otpExpired($otp)) {
            throw new Exception("Otp Expired", Response::HTTP_BAD_REQUEST);
        }
    }
    public function otpExpired(Otp $otp)
    {
        $createdTime = Carbon::parse($otp->created_at);
        if ($createdTime->diffInMinutes(Carbon::now()) >= Otp::OTP_EXPIRE_TIME_MINUTES) {
            return true;
        }
        return false;
    }
    
}
