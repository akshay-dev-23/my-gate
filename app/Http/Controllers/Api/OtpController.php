<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OtpController extends Controller
{
    /**
     * Send otp api
     * @param Request $request 
     * @param OtpService $otpService 
     * @return JsonResponse 
     * @throws Exception 
     * @throws BindingResolutionException 
     */
    public function sendOtp(Request $request, OtpService $otpService)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required'
        ]);
        if ($validator->fails())
            throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        $otp = $otpService->generateOTP();
        if (!$otpService->sendOTP($request->mobile_number, $otp))
            throw new Exception('Otp not send.Please try again.', Response::HTTP_INTERNAL_SERVER_ERROR);
        $otpService->storeOTP($request->mobile_number, $otp);
        return $this->successResponse("Otp sent successfully");
    }
    /**
     * 
     * @param Request $request 
     * @param OtpService $otpService 
     * @return JsonResponse 
     * @throws Exception 
     */
    public function verifyOTP(Request $request, OtpService $otpService)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'otp_code' => 'required'
        ]);
        if ($validator->fails())
            throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        $otpService->verifyOTP($request->mobile_number,$request->otp_code);
        return $this->successResponse("Otp verified successfully.");
    }
}
