<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FcmService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $users = User::societyUser($user->society_id, $user->id)->get();
        if (!$users->count() > 0) throw new Exception("Members not found.", Response::HTTP_NOT_FOUND);
        return $this->successResponse("Members listing", UserResource::collection($users));
    }

    /**
     * This api used to verify the user
     * @param Request $request 
     * @return JsonResponse 
     * @throws Exception 
     * @throws BindingResolutionException 
     */
    public function verifyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'verify' => 'required|boolean'
        ]);
        if ($validator->fails())
            throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        $auth = Auth::user();
        $user = User::societyUser($auth->society_id, $auth->id)->whereId($request->user_id)->first();
        if (!$user) throw new Exception("Record not found", Response::HTTP_NOT_FOUND);
        $user->verified = $request->verify;
        $user->save();
        FcmService::accountStatusChangeNotification($user, $request->verify);
        return $this->successResponse("Status changed successfully.");
    }

    /**
     * This is api call to send the user details in response
     * @return JsonResponse 
     * @throws BindingResolutionException 
     */
    public function authUser()
    {
        $user = Auth::user()->load(['roles']);
        return $this->successResponse("Login successful.", ['user' => new UserResource($user)]);
    }
}
