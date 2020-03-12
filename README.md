### Laravel-VUE-Admin

**安装**
1. 添加提供者到config/app.php。
```php
'providers' => [

    ...

    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
    Jmhc\Admin\AdminServiceProvider::class,
]
```
2. 发布资源
```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

// 这一行命令会覆盖routes/api.php和 RouteServiceProvider.php
php artisan vendor:publish --provider="Jmhc\Admin\AdminServiceProvider" --froce
```

3. 迁移数据
```bash
php artisan migrate
```

4. 生成jwt秘钥
```bash
php artisan jwt:secret
```
