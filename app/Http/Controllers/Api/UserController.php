<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{

    //返回用户列表
    public function index()
    {
        //3个用户为一页
        $users = User::paginate(3);
        return UserResource::collection($users);
        //这里不能用$this->success(UserResource::collection($users))
        //否则不能返回分页标签信息
    }

    //返回单一用户信息
    public function show(User $user)
    {
        return $this->success(new UserResource($user));
    }

    //用户注册
    public function store(UserRequest $request)
    {
        User::create($request->all());
        return $this->setStatusCode(201)->success('用户注册成功');
    }

    //用户登录
    public function login(Request $request)
    {
        $token = Auth::guard('api')->attempt(['name' => $request->name, 'password' => $request->password]);
        if ($token) {
            // 存储登陆用户token
            $user = User::whereName($request->input('name'))->first();
            Redis::setex('login_token_' . $user->id, env('JWT_TTL') * 60, $token);
            return $this->setStatusCode(201)->success(['token' => 'bearer ' . $token]);
        }
        return $this->failed('账号或密码错误', 400);
    }

    //用户退出
    public function logout()
    {
        $user = Auth::guard('api')->user();
        Redis::del('login_token_' . $user->id, -1, ""); // 删除存储的token
        //Auth::guard('api')->logout();
        return $this->success('退出成功...');
    }

    //返回当前登录用户信息
    public function info()
    {
        $user = Auth::guard('api')->user();
        return $this->success(new UserResource($user));
    }
}
