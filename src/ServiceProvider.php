<?php

namespace Reports;

use Illuminate\Support\ServiceProvider as Provider;
use Reports\Controllers\Http\{ReportsController, ReportController};
use Illuminate\Support\Facades\Response;
use Reports\Reports;
use Reports\Contracts\Response as ResponseContract;

class ServiceProvider extends Provider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutes();
        $this->bootResponseMacros();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'reports');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/reports'),
        ], 'views');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/reports'),
        ], 'public');

        $this->publishes([
            __DIR__.'/../config/reports.php' => config_path('reports.php'),
        ], 'config');

        if ($this->app->runningInConsole() && $this->app->environment('local') && $this->app['files']->isDirectory(base_path('../laravel-reports')) ) {
            $this->commands([
                BuildCommand::class
            ]);
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Reports\Reports', function ($app) {
            return new Reports($app);
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/reports.php', 'reports'
        );

        $this->app['config']->set(
            'filesystems.disks.examples', [
                'driver' => 'local',
                'root' => __DIR__ . '/../examples',
            ]
        );
    }

    private function loadRoutes()
    {
        $this->app['router']->middleware('web')->namespace('Reports')->group(function () {

            $this->app['router']->get('/reports', 'Controller@reports')
                ->name('reports.list');

            $this->app['router']->get('report/{type?}', 'Controller@report')
                ->where('type', 'html|json|text|xlsx|jsonh')
                ->name('reports.show');
        });
    }

    private function bootResponseMacros()
    {
        foreach ($this->app['files']->files(__DIR__ . '/Responses') as $file) {
            if ($file->getFilename() == "AbstractResponse.php") {
                continue;
            }

            $class = pathinfo($file, PATHINFO_FILENAME);
            $name = strtolower(str_replace('Response', '', $class));
            $class = '\Reports\Responses\\' . $class;
            $class = new $class;

            if ($class instanceof ResponseContract) {
                Response::macro($name, function ($value) use ($class) {
                    return $class->make($value);
                });
            }

        }
    }
}
