## Laravel-Vue-Admin
![image](https://s1.ax1x.com/2020/06/29/NfET2j.png)
Laravel-Vue-Admin主要优势在于前后端分离，具有一键生成功能，页面公共组件强大。后端接口逻辑部分，也就是Service通过Laravel容器进行管理，发挥出Laravel的优势。后端不必再做许多重复的工作，为喜欢Vue的后端php工程师提供便利。laravel-vue-admin参考了许多类似的项目，使用起来都很方便。例如Larave-Admin、FastAdmin，都能快速开发后台。而Laravel-vue-admin也主要是因我个人开发习惯才产生的想法。

### Laravel-vue-Admin中项目文档
* [vue-element-admin](https://panjiachen.github.io/vue-element-admin-site/zh/)
* [jwt-auth](https://jwt-auth.readthedocs.io/en/develop/) 
* [laravel-permission](https://docs.spatie.be/laravel-permission/v3/introduction/)
* [Fractal](https://fractal.thephpleague.com/)

### 码云仓库地址
[gitee.com](https://gitee.com/jf_aa/laravel-vue-admin)

### 安装
```bash
composer require frey/laravel-vue-admin
```
### 配置

##### 添加提供者到config/app.php。
```php
'providers' => [

    //...

    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
    Jmhc\Admin\AdminServiceProvider::class,
]
```
##### 发布资源

```bash
php artisan vendor:publish --force
// 选择发布Tymon\JWTAuth\Providers\LaravelServiceProvider和Jmhc\Admin\AdminServiceProvider
```
##### 数据库配置

在数据库中新好数据库，在.env中进行配置
```dotenv
...
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=123456
...
```
##### 配置config/auth.php
```php
// 在guards中增加admin
    'guards' => [
        'admin' => [
            'driver' => 'jwt',
            'provider' => 'admin_users',
        ],
       
    ],
...
// 在providers中增加admin_users
    'providers' => [
        'admin_users' => [
             'driver' => 'eloquent',
             'model' => Jmhc\Admin\Models\Auth\AdminUser::class,
                ],
    ]
```

##### 导入数据

运行如下命令导入后台数据表
```bash
php artisan admin:import
```

##### 生成jwt秘钥
```bash
php artisan jwt:secret
```

##### 配置中间件
需要在app/Http/Kernel.php中增加如下中间件
```php
 // 1. 一定要注释掉$middleware中将空字符串转换为null的中间件
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        //注释这一行↓
//        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

 // 2. 在$middlewareGroups数组中的api下面增加
   ...
'api' => [
    \Jmhc\Admin\Middleware\LogOperation::class,
],
...
 // 3. 在$routeMiddleware增加如下中间件
...
    'service' => \Jmhc\Admin\Middleware\HasService::class,
    'permission' => \Jmhc\Admin\Middleware\CheckPermission::class,
...
```

### 命令
##### 一键生成命令
```bash
php artisan admin:generate table_name --model=TableName --module=Admin --view --force
```
* --model 命名模型
* --module 模块名称，如Admin、Api
* --view 此参数存在表示生成视图文件，在resources/page/view下
* --force 此参数存在则表示强制覆盖

##### 重置管理员密码
```bash
php artisan admin:password account password
// 如
php artisan admin:password admin 123456
```

##### 测试运行

需要在数据库中创建一个数据表，如test，数据迁移如下：
```php
Schema::create('test', function (Blueprint $table) {
    $table->id();
    $table->string('name', '15');
    $table->string('status')->comment('状态:0=否,1=是');
    $table->timestamps();
});

```
迁移数据
```bash
php artisan migrate 
```
运行生成命令
```bash
php artisan admin:generate test --model=Test
```
在routes/admin.php中会生成对应的路由，需要给它们加上service中间件，建议放在group下面，就不用每一个都写一个->middleware('service')，可以参考vendor/frey/laravel-vue-admin/routes/route.php路由配置，例：
```php
Route::middleware(['service'])->group(function() {
    // Test
    Route::resource('test', 'TestController');

});

```

permission中间件为权限验证中间件，使用时需要传入守卫名称，如permission:admin，每一个路由都是一个操作，他们的标识符为路由名称。资源路由默认以“接口名称.index”形式作为路由名称。

浏览器访问: http://yourdomain.php/admin/test 就可以看到效果了。

##### 文件系统设置
在.env里面配置文件系统驱动
```dotenv
FILESYSTEM_DRIVER=public
```
然后运行命令
```bash
php artisan storage:link
```

##### 其他配置
jwt默认生命周期为1小时，也就是60分钟，如果想要调整，需要在.env中配置
```dotenv
# 单位为分钟
JWT_TTL=1440
```

### 前端

前端视图页面放在resources/page里面，需要在根目录中的.env中加入如下配置：
```.dotenv
# 接口基础路径
VUE_APP_BASE_API=/admin
# 图片显示域名
VUE_APP_ASSETS_URL=http://yourdomain.com/
```
运行：
```bash
npm install
npm run dev
```
代理配置在根目录下的vue.config.js中，可以运行npm run build:prod编译，需要在routes/web.php 中修改路由，
```php
Route::get('/', function () {
    return view('index');
});

```
编译之后就可以通过访问域名来访问后台了。

##### 组件使用
主要的两大组件为：PowerfulTable、PowerfulForm。一个用于表格显示，一个用于表单显示。他们位于resources/page/components下，应用参考views/auth或者views/system页面组件。
