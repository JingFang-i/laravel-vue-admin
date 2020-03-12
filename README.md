# Laravel-VUE-Admin
laravel-vue-admin整合了laravel、vue-element-admin。优势在于前后端分离，且不用分离成两个项目开发。使用了tymon/jwt-auth进行登录状态验证和laravel-permission进行权限验证。还有容器自动加载Service和Repository，只需要专注业务逻辑，减少了大量的重复性开发。

### 安装
```composer
composer require frey/laravel-vue-admin
```
* 添加提供者到config/app.php。

```php
'providers' => [

    ...

    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
    Jmhc\Admin\AdminServiceProvider::class,
]
```
* 发布资源

```bash
// jwt-auth 资源发布
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

// 这一行命令会覆盖routes/api.php和 RouteServiceProvider.php
php artisan vendor:publish --provider="Jmhc\Admin\AdminServiceProvider" --force
```

* 迁移数据
```bash
php artisan migrate
```

* 生成jwt秘钥
```bash
php artisan jwt:secret
```
### 配置
* 配置好数据库账号和密码，在.env中进行配置。
```.dotenv
...
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=123456
...
```
* 配置config/auth.php
```php
        'defaults' => [
            'guard' => 'admin',
            'passwords' => 'users',
        ],
...

        'guards' => [
            'admin' => [
                'driver' => 'jwt',
                'provider' => 'admin_users',
            ],
        
        ],
        
        'providers' => [
            'admin_users' => [
                'driver' => 'eloquent',
                'model' => App\Models\Auth\AdminUser::class,
            ],
    
        ],

```
* 测试是否配置成功

服务器配置好后，通过浏览器访问```http://localdomain.com/admin/v1/test```，如果看到```Test is successful.```表示配置成功。

### 命令
* 导入sql
```bash
php artisan admin:import
```
* 一键生成
```bash
php artisan admin:generate table_name [--model=ModelName] --force
```
```table_name``` 为数据库表名，```model```为模型名称，可以带路径如：Order/OrderGoods。如果要覆盖，则需要带上```--force```。
一件生成命令会自动创建资源控制器，以及对应的模型、服务、仓储类。


### 前端

在.env文件里追加```VUE_APP_BASE_API=/admin```，再修改vue.config.js中的代理配置target，如下。
```javascript
devServer: {
    port: port,
    open: true,
    overlay: {
      warnings: false,
      errors: true
    },
    // before: require('./mock/mock-server.js'),
    proxy: {
      '/admin': {
        target: 'http://yourlocaldomain/admin', // 本地后端配好的域名
        ws: false,
        changeOrigin: true,
        pathRewrite: {
          '^/admin': '/'
        }
      }
    }
  },
```
前端页面在resources/page下，参考[vue-element-admin](https://panjiachen.github.io/vue-element-admin-site/zh/)文档。
直接运行
```bash
npm run dev
```
