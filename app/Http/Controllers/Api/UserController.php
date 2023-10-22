<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
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

    public function verifyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'verify' => 'required|boolean'
        ]);
        if ($validator->fails())
            throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        $user = User::find($request->user_id);
        if (!$user) throw new Exception("Record not found", Response::HTTP_NOT_FOUND);
        $user->verified = $request->verify;
        $user->save();
        return $this->successResponse("Status changed successfully.");
    }
}
