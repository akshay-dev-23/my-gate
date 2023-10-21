<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    /**
     * this function  is used to send the response to the api
     * @param mixed $message 
     * @param array $data 
     * @param int $code 
     * @return JsonResponse 
     * @throws BindingResolutionException 
     */
    protected function successResponse($message, $data = [], $code = Response::HTTP_OK)
    {
        return response()->json([
            'message' => $message,
            'data'=>$data
        ], $code);
    }
}
