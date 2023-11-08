<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Otp;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;


class OtpService
{
    public function generateOTP()
    {
        $characters = '0123456789';
        $otp = '';
        for ($i = 0; $i < 4; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $otp;
    }

    public function authMessage($otp)
    {
        $otp_expire_time = Otp::OTP_EXPIRE_TIME_MINUTES;
        return "Your OTP is $otp .It will be valid for $otp_expire_time minutes only.";
    }

    /**
     * This method  to send the otp to a number
     * @param mixed $mobile_number 
     * @param mixed $message 
     * @return true 
     * @throws BindingResolutionException 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws TwilioException 
     */
    public function sendOTP($mobile_number, $message)
    {
        $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $twilio->messages->create($mobile_number, [
            'from' => config('services.twilio.phone_number'),
            'body' => $message,
        ]);
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

    /**
     * Check if otp is expired or not.
     * @param Otp $otp 
     * @return bool 
     * @throws InvalidFormatException 
     */
    private function otpExpired(Otp $otp)
    {
        $createdTime = Carbon::parse($otp->created_at);
        if ($createdTime->diffInMinutes(Carbon::now()) >= Otp::OTP_EXPIRE_TIME_MINUTES) {
            return true;
        }
        return false;
    }

    /**
     * This function is used to revoke all the mobile otps
     * @param mixed $mobile_number 
     * @return mixed 
     */
    public function revokeAllOtp($mobile_number)
    {
        return  Otp::where(['mobile_number' => $mobile_number])->delete();
    }
}
