<?php

namespace Reports;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use lessc;
use MatthiasMullie\Minify\{CSS, JS};
use JasonLewis\ResourceWatcher\{Tracker, Watcher};

class BuildCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:build {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build Distribution';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $file)
    {
        parent::__construct();
        $this->file = $file;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        switch ($this->argument('action')) {
            case 'css': $this->less(); return;
            case 'deps': $this->deps(); return;
            case 'js': $this->js(); return;
            case 'watch': $this->watch(); return;
            case 'release':
                $this->clean();
                $this->deps();
                $this->js();
                $this->less();
                $this->minify();
                return;
        }
    }

    private function clean()
    {
        foreach (['css', 'js', 'fonts'] as $dir) {
            $this->file->cleanDirectory(base_path('vendor/hfletcher/laravel-reports/public/' . $dir));
        }
    }

    private function less()
    {
        $less = new lessc;
        $less->checkedCompile(
            base_path('vendor/hfletcher/laravel-reports/resources/assets/less/app.less'),
            public_path('vendor/reports/css/app.css')
        );
        $less->checkedCompile(
            base_path('vendor/hfletcher/laravel-reports/resources/assets/less/reports.less'),
            public_path('vendor/reports/css/reports.css')
        );
    }

    private function deps()
    {

        $this->bulkCopy([
            base_path('vendor/almasaeed2010/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js') => 'js/bootstrap.min.js',
            base_path('vendor/almasaeed2010/adminlte/dist/js/adminlte.min.js') => 'js/adminlte.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css') => 'css/bootstrap.min.css',
            base_path('vendor/almasaeed2010/adminlte/dist/css/AdminLTE.min.css') => 'css/AdminLTE.min.css',
            base_path('vendor/almasaeed2010/adminlte/dist/css/skins/skin-blue-light.min.css') => 'css/skin-blue-light.min.css',
            base_path('vendor/almasaeed2010/adminlte/bower_components/jquery/dist/jquery.min.js') => 'js/jquery.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js') => 'js/jquery.dataTables.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') => 'js/dataTables.bootstrap.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') => 'css/dataTables.bootstrap.min.css',
            base_path('vendor/almasaeed2010/adminlte/bower_components/bootstrap/fonts') => 'fonts',
            base_path('vendor/maxazan/jquery-treegrid/js/jquery.treegrid.min.js') => 'js/jquery.treegrid.min.js',
            base_path('vendor/maxazan/jquery-treegrid/js/jquery.treegrid.bootstrap3.js') => 'js/jquery.treegrid.bootstrap3.js',
            base_path('vendor/maxazan/jquery-treegrid/css/jquery.treegrid.css') => 'css/jquery.treegrid.css',
            base_path('vendor/jmosbech/StickyTableHeaders/js/jquery.stickytableheaders.min.js') => 'js/jquery.stickytableheaders.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/moment/min/moment.min.js') => 'js/moment.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.js') => 'js/daterangepicker.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.css') => 'css/daterangepicker.css',
            base_path('vendor/google/code-prettify/loader/prettify.js') => 'js/prettify.js',
            base_path('vendor/google/code-prettify/loader/prettify.css') => 'css/prettify.css',
        ]);
    }

    private function bulkCopy($array)
    {
        foreach ($array as $src => $dest) {
            if ($this->file->isDirectory($src)) {
                $this->file->copyDirectory($src, base_path('vendor/hfletcher/laravel-reports/public') . '/' . $dest);
                continue;
            }
            if ($this->file->isFile($src)) {
                $this->file->copy($src, base_path('vendor/hfletcher/laravel-reports/public') . '/' . $dest);
                continue;
            }
            throw new \Exception($src . ' does not exist as directory or file.');
        }
    }

    private function js()
    {
        $files = $this->file->files(base_path('vendor/hfletcher/laravel-reports/resources/assets/js'));
        $this->file->put(public_path('vendor/reports/js/reports.js'), null);
        foreach ($files as $file) {
            $this->file->append(public_path('vendor/reports/js/reports.js'), $file->getContents());
        }
    }

    private function minify()
    {
        $this->file->copy(public_path('vendor/reports/css/reports.css'), public_path('vendor/reports/css/reports.css.temp'));
        $minifier = (new CSS(public_path('vendor/reports/css/reports.css.temp')))->minify(base_path('vendor/hfletcher/laravel-reports/public/css/reports.css'));

        $this->file->copy(public_path('vendor/reports/js/reports.js'), public_path('vendor/reports/js/reports.js.temp'));
        $minifier = (new JS(public_path('vendor/reports/js/reports.js.temp')))->minify(base_path('vendor/hfletcher/laravel-reports/public/js/reports.js'));
    }

    private function watch()
    {
        $this->line('Building ' . base_path('vendor/hfletcher/laravel-reports/resources/assets/js'));
        $this->js();
        $this->line('Building ' . base_path('vendor/hfletcher/laravel-reports/resources/assets/less'));
        $this->less();
        $this->info('Watching ' . base_path('vendor/hfletcher/laravel-reports/resources'));

        $tracker = new Tracker;
        $watcher = new Watcher($tracker, $this->file);

        $listener = $watcher->watch(base_path('vendor/hfletcher/laravel-reports/resources/assets'));
        $listener->modify(function($resource, $path) {
            $this->info($path);
            if (substr($path, 0, strlen('/var/www/laravel-reports/resources/assets/js')) === '/var/www/laravel-reports/resources/assets/js') {
                $this->line('Modify of '. $path);
                $this->js();
            }
            if (substr($path, 0, strlen('/var/www/laravel-reports/resources/assets/less')) === '/var/www/laravel-reports/resources/assets/less') {
                $this->line('Modify of '. $path);
                $this->less();
            }

        });
        $watcher->start();
    }

}
