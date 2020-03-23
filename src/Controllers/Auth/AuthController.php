<?php


namespace Jmhc\Admin\Controllers\Auth;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Jmhc\Admin\Services\Auth\AdminUserService;

class AuthController extends Controller
{
    protected $response;
    protected $authManager;

    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
        $this->authManager = auth('admin');
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['username', 'password']);
        $validator = $this->validateCredentials($credentials);

        if ($validator->fails()) {
            return $this->response->error($validator->errors()->first());
        }

        if (! $token = $this->authManager->attempt($credentials)) {
            return $this->response->error('用户名或密码不正确');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        return $this->response->success($this->authManager->user()->toArray());
    }

    /**
     * 更新信息
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function updateInfo()
    {
        return AdminUserService::instance()->updateSelf();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->authManager->logout();

        return $this->response->success();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->authManager->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return $this->response->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->authManager->factory()->getTTL() * 60
        ]);
    }

    /**
     * 验证登录凭证
     *
     * @param $credentials
     * @return mixed
     */
    protected function validateCredentials($credentials)
    {
        $validator = Validator::make($credentials, [
            'username' => ['required', 'regex:/^[a-zA-Z0-9_@\.]{5,20}$/'],
            'password' => ['required', 'min:6'],
        ], [
            'username.required' => '请输入用户名',
            'password.required' => '请输入密码',
            'username.regex' => '用户名只能为数字字母或者_@.符号',
        ]);
        return $validator;
    }
}
