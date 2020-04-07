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
     * 设置守卫名称
     * @param string $name
     */
    public static function setGuard(string $name)
    {
        self::$guard = $name;
    }

    /**
     * 获取守卫名称
     * @return string
     */
    public static function getGuard(): string
    {
        return self::$guard;
    }

}
