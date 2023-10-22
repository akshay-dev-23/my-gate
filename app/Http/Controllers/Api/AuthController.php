<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Society;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|unique:users|max:10',
            'password' => 'required|string|min:6|max:10',
            'society_code' => 'required|string',
            'name' => 'required|string|max:20',
            'flat_no' => 'required|string|max:10'
        ]);
        if ($validator->fails())
            throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        $society = Society::where('code', $request->society_code)->first();
        if (!$society) throw new Exception("Invalid society code.", Response::HTTP_BAD_REQUEST);
        $user = User::create($this->getUserData($request->only('mobile_number', 'password', 'name', 'flat_no'), $society->id));
        $user->assignRole('user');
        return response()->json(['message' => 'Successfully registered.'], 200);
    }

    /**
     * this function is used to login the user
     * @param Request $request 
     * @return JsonResponse 
     * @throws BindingResolutionException 
     */
    public function login(Request $request)
    {
        $credentials = $request->only('mobile_number', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->verified) throw new Exception("Account not verified. Please connect to admin to verify the account.", Response::HTTP_FORBIDDEN);
            $token = $user->createToken('MyApp')->accessToken;
            return response()->json(['access_token' => $token], 200);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
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
}
