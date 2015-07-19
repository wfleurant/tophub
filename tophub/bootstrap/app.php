<?php

require_once __DIR__.'/../vendor/autoload.php';

Dotenv::load(__DIR__.'/../');

/*
| Create The Application
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);


/*
| Register Container Bindings
	$app->withFacades();
*/
$app->register('Jenssegers\Mongodb\MongodbServiceProvider');
$app->withEloquent();

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
| Register Middleware
	$app->middleware([
		Illuminate\Cookie\Middleware\EncryptCookies::class,
		Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
		Illuminate\Session\Middleware\StartSession::class,
		Illuminate\View\Middleware\ShareErrorsFromSession::class,
		Laravel\Lumen\Http\Middleware\VerifyCsrfToken::class,
	$app->routeMiddleware([

	]);
*/
]);


/*
| Register Service Providers
	$app->register(App\Providers\AppServiceProvider::class);
	$app->register(App\Providers\EventServiceProvider::class);
*/


/*
| Load The Application Routes
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../app/Http/routes.php';
});

return $app;
