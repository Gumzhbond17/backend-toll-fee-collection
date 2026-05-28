<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HelperController extends Controller
{
    // request validation
    public function validated(Request $request, mixed $rule)
    {
        $validateRequest = Validator::make($request->all(), $rule);
        if ($validateRequest->fails()) {
            throw new \Exception($validateRequest->errors()->first());
        }
    }

    // response messages
    public function response(string $message, mixed $data, int $status)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    // pagination
    public function paginate(mixed $model, mixed $limit)
    {
        $page = request()->page ?? 1;
        $limit = request()->limit ?? 10;

        return $model->paginate($limit, ['*'], 'page', $page);
    }
}
