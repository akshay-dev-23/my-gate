<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Http\Request;


class DeviceService
{
    /**
     * The changes register the device for the user
     * @param mixed $user_id 
     * @param Request $request 
     * @return bool 
     */
    public function register($user_id)
    {
        $request = request();
        $platform = $request->header('Platform', null);
        $device_token = $request->header('Device-Token', null);
        DeviceToken::where('user_id', $user_id)->delete();
        DeviceToken::create([
            'user_id' => $user_id, // If you want to associate the token with a user
            'token' => $device_token,
            'platform' => $platform,
        ]);
        return true;
    }

    /**
     * This is used to delete the device token for the user
     * @param mixed $user_id 
     * @return bool 
     */
    public function delete($user_id)
    {
        DeviceToken::where('user_id', $user_id)->delete();
        return true;
    }
}
