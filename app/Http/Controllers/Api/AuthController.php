<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Society;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\DeviceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * This is api call to register the user
     * @param Request $request 
     * @param DeviceService $device 
     * @return JsonResponse 
     * @throws Exception 
     * @throws BindingResolutionException 
     */
    public function register(Request $request, DeviceService $device)
    {
        $validator = Validator::make($request->all(), $this->getRegisterValidation());
        if ($validator->fails())
            throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        $society = Society::where('code', $request->society_code)->first();
        if (!$society) throw new Exception("Invalid society code.", Response::HTTP_BAD_REQUEST);
        $user = User::create($this->getUserData($request->only('mobile_number', 'password', 'name', 'flat_no'), $society->id));
        $user->assignRole('user');
        $device->register($user->id, $request);
        $token = $user->createToken('MyApp')->accessToken;
        return $this->successResponse("Successfully registered.", ['access_token' => $token, 'user' => new UserResource($user)]);
    }

    /**
     * this function is used to login the user
     * @param Request $request 
     * @return JsonResponse 
     * @throws BindingResolutionException 
     */
    public function login(Request $request, DeviceService $device)
    {
        $validator = Validator::make($request->all(), ['mobile_number' => 'required', 'password' => 'required']);
        if ($validator->fails())
            throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        $credentials = $request->only('mobile_number', 'password');
        if (!Auth::attempt($credentials)) throw new Exception('Invalid credentials', Response::HTTP_BAD_REQUEST);
        $user = Auth::user()->load(['roles']);
        $this->revokeToken($user->id);
        $token = $user->createToken('MyApp')->accessToken;
        return $this->successResponse("Login successful.", ['access_token' => $token, 'user' => new UserResource($user)]);
    }

    /**
     * This function is used to get the user data
     * @param mixed $request_data 
     * @param mixed $society_id 
     * @return mixed 
     */
    private function getUserData($request_data, $society_id)
    {
        $request_data['society_id'] = $society_id;
        $request_data['verified'] = false;
        return $request_data;
    }

    /**
     * This function used to revoke the other all user token
     * @param mixed $user_id 
     * @return void 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    private function revokeToken($user_id)
    {
        DB::table('oauth_access_tokens')
            ->where('user_id', $user_id)
            ->update(['revoked' => true]);
    }

    /**
     * This function is used to register validation
     * @return string[] 
     */
    private function getRegisterValidation()
    {
        return [
            'mobile_number' => 'required|string|unique:users|max:15',
            'password' => 'required|string|min:6|max:20',
            'society_code' => 'required|string',
            'name' => 'required|string|max:20',
            'flat_no' => 'required|string|max:10'
        ];
    }
}
