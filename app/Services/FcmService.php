<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class FcmService
{
    /**
     * This function is used to call the api internally
     * @param mixed $user_fcm_token 
     * @param mixed $fcm_server_key 
     * @param array $data 
     * @return ResponseInterface 
     * @throws GuzzleException 
     */
    private static function send($user_fcm_token, $fcm_server_key, array $data)
    {
        $request_body = [
            'headers' => ['Content-Type' => 'application/json', 'authorization' => 'Bearer ' . $fcm_server_key],
            'json' => [
                'to' => $user_fcm_token,
                'data' => $data
            ]
        ];
        $client = new \GuzzleHttp\Client();
        $url = "https://fcm.googleapis.com/fcm/send";
        return $client->request('POST', $url, $request_body);
    }


    /**
     * This function is used to send the account status change notification to user
     * @param User $user 
     * @param mixed $is_verified 
     * @return bool 
     * @throws NotFoundExceptionInterface 
     * @throws ContainerExceptionInterface 
     * @throws GuzzleException 
     */
    public static function accountStatusChangeNotification(User $user, $is_verified)
    {
        if ($is_verified) {
            $title = 'Account Verified.';
            $message = 'Your account successfully verified.Take a look into your society';
        } else {
            $title = 'Account Not Verified.';
            $message = 'Your account is not verified.Please contact admin to approve the account.';
        }
        $data = [
            "title" => $title,
            "body" => $message,
            "is_approved" => $is_verified,
            "user_id" => $user->id
        ];
        try {
            $fcm_server_key = config('services.fcm.user_server_key');
            $devices = $user->device()->get();
            if ($devices->count() == 0) return false;
            foreach ($devices as $device) {
                if ($device->device_token == null) continue;
                self::send($device->device_token, $fcm_server_key, $data);
            }
            return true;
        } catch (\Exception $exe) {
            return false;
        }
    }
}
