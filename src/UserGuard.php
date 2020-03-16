<?php


namespace Jmhc\Admin;


class UserGuard
{
    protected static $user = null;

    protected static $guard = '';

    /**
     * 获取用户对象
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function getUser()
    {
        if (empty(self::$user)) {
            $guard = self::getGuard();
            self::$user = auth($guard)->user();
        }
        return self::$user;

    }

    /**
     * 获取守卫名称
     * @return string
     */
    public static function getGuard()
    {
        if (empty(self::$guard)) {
            $routePrefix = request()->route()->getPrefix();
            if (strpos($routePrefix, 'admin') !== false) {
                self::$guard = 'admin';
            } else {
                self::$guard = 'api';
            }
        }
        return self::$guard;
    }

}
