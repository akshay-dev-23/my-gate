<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max size as needed
        ]);
        if ($validator->fails())
            throw new Exception($validator->errors()->first(), Response::HTTP_BAD_REQUEST);
        $user = Auth::user()->load(['society']); // Assuming you are using authentication
        $societyCode = $user->society->code; // You may adjust this based on your needs
        $userId = $user->id;
        $dateFolder = now()->format('Y-m-d');
        $basePath = "images/{$societyCode}/{$userId}/{$dateFolder}";
        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension();
        $image->storeAs($basePath, $imageName, 'public');
        $imagePath = asset("storage/{$basePath}/{$imageName}");
        return $this->successResponse("Images uploaded successfully.", ['image_path' => $imagePath]);
    }
}
