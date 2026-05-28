<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected HelperController $helper;

    public function __construct()
    {
        $this->helper = new HelperController;
    }

    public function register(Request $request)
    {
        try {
            // ✅ ກຳນົດກົດລະບຽບການກວດສອບຂໍ້ມູນ (Validation Rules)
            $rule = [
                'username' => 'required|string',
                'fullname' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|in:Admin,Data-Entry,Checker,Approver,Viewer',
                'site_id' => 'required|exists:sites,id',
                'department_id' => 'required|exists:departments,id',
                'is_active' => 'boolean',
            ];

            // ✅ ສົ່ງ request ແລະ rules ໄປກວດສອບຜ່ານ HelperController
            // ຖ້າຂໍ້ມູນບໍ່ຜ່ານ validation ຈະ throw exception ອັດຕະໂນມັດ
            $this->helper->validated($request, $rule);

            // ກຽມຂໍ້ມູນສຳລັບສ້າງ User ໃໝ່
            $obj = [
                'username' => $request->username,
                'fullname' => $request->fullname,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
                'site_id' => $request->site_id,
                'department_id' => $request->department_id,
                'is_active' => $request->is_active ?? true,
            ];

            // ✅ ສ້າງ User ໃໝ່ໃນ Database ດ້ວຍຂໍ້ມູນໃນ $obj
            $user = User::create($obj);

            // ✅ ສ້າງ JWT Token ສຳລັບ User ທີ່ຫາກໍ່ສ້າງ
            // Token ນີ້ໃຊ້ສຳລັບ Authentication ໃນ API calls ຕໍ່ໄປ
            $token = JWTAuth::fromUser($user);

            // ✅ ສົ່ງ Response ກັບໄປຫາ Client
            // - message: 'User created successfully'
            // - data: { user: {...}, token: 'xxx' }  ← compact() ສ້າງ array ຈາກ variables
            // - status code: 201 (Created)
            return $this->helper->response(
                'User created successfully',
                compact('user', 'token'), // = ['user' => $user, 'token' => $token]
                201
            );
        } catch (\Throwable $th) {
            // ✅ ດັກຈັບ Error ທຸກປະເພດ (Exception, Error, ...)
            // ສົ່ງ error message ກັບໄປຫາ Client
            // - message: error ທີ່ເກີດຂຶ້ນ
            // - data: null (ບໍ່ມີຂໍ້ມູນ)
            // - status code: 500 (Internal Server Error)
            return $this->helper->response($th->getMessage(), '', 500);
        }
    }

    public function login(Request $request)
    {
        // ✅ ດຶງສະເພາະ email ແລະ password ຈາກ request
        $credentials = $request->only('email', 'password');

        try {
            // ✅ ກວດສອບ credentials — ຖ້າຜິດຈະ return false
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Invalid credentials', // ແກ້ typo: creadentials → credentials
                ], 401);
            }

            // ✅ ດຶງ User ປັດຈຸບັນຜ່ານ JWT guard
            $user = JWTAuth::user();

            // ✅ ສ້າງ Token ໃໝ່ພ້ອມ custom claims
            $token = JWTAuth::claims([
                'username' => $user->username, // ✅ ໃຊ້ username ຕາມ schema ທີ່ກຳນົດ
            ])->fromUser($user);

            return $this->helper->response(
                'User logged in successfully',
                compact('user', 'token'),
                200
            );

        } catch (\Throwable $th) {
            return $this->helper->response($th->getMessage(), '', 500);
        }
    }

    public function me()
    {
        // ✅ ກວດສອບ authentication ກ່ອນ
        $user = auth('api')->user();

        if (! $user) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user,
        ], 200);
    }

    public function logout()
    {
        try {
            // ✅ ດັກຈັບ error ກໍລະນີ Token ໝົດອາຍຸ ຫຼື ບໍ່ valid
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Logged out successfully!',
            ], 200);

        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token is invalid',
            ], 401);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function respondWithToken(mixed $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // ✅ ດຶງໂດຍກົງຈາກ config/jwt.php
            'expires_in' => config('jwt.ttl') * 60,
        ], 200);
    }

    public function refresh()
    {
        try {
            // ✅ ໃຊ້ JWTAuth facade ໂດຍກົງ
            $newToken = JWTAuth::parseToken()->refresh();

            return $this->respondWithToken($newToken);

        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Token is invalid',
            ], 401);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired',
            ], 401);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
