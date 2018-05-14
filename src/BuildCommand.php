<?php

namespace Reports;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use lessc;

class BuildCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:build {--less} {--copy}';

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
        if ($this->option('less')) {
            $this->less();
        }
        if ($this->option('copy')) {
            $this->copy();
        }
        if(!$this->option('less') && !$this->option('copy')) {
            $this->file->cleanDirectory(base_path('vendor/hfletcher/laravel-reports/public/css'));
            $this->copy();
            $this->less();

        }

    }

    private function less()
    {
        $less = new lessc;
        $less->checkedCompile(
            base_path('vendor/hfletcher/laravel-reports/resources/assets/less/app.less'),
            public_path('vendor/reports/css/reports.css')
        );
    }

    private function copy()
    {

        foreach ($this->file->glob(base_path('vendor/thomaspark/bootswatch/*/bootstrap.min.css')) as $dir) {
            $name = pathinfo(pathinfo($dir, PATHINFO_DIRNAME), PATHINFO_BASENAME);
            $this->file->copy($dir, base_path('vendor/hfletcher/laravel-reports/public/css/bootstrap.' . $name . '.min.css'));
        }

        $this->bulkCopy([
            base_path('vendor/almasaeed2010/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js') => 'js/bootstrap.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/jquery/dist/jquery.min.js') => 'js/jquery.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js') => 'js/jquery.dataTables.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') => 'js/dataTables.bootstrap.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') => 'css/dataTables.bootstrap.min.css',
            base_path('vendor/almasaeed2010/adminlte/bower_components/bootstrap/fonts') => 'fonts',
            base_path('vendor/maxazan/jquery-treegrid/js/jquery.treegrid.min.js') => 'js/jquery.treegrid.min.js',
            base_path('vendor/maxazan/jquery-treegrid/js/jquery.treegrid.bootstrap3.js') => 'js/jquery.treegrid.bootstrap3.js',
            base_path('vendor/maxazan/jquery-treegrid/css/jquery.treegrid.css') => 'css/jquery.treegrid.css',
            base_path('vendor/bower-asset/stickytableheaders/js/jquery.stickytableheaders.min.js') => 'js/jquery.stickytableheaders.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/moment/min/moment.min.js') => 'js/moment.min.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.js') => 'js/daterangepicker.js',
            base_path('vendor/almasaeed2010/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.css') => 'css/daterangepicker.css',
            base_path('vendor/bower-asset/code-prettify/loader/prettify.js') => 'js/prettify.js',
            base_path('vendor/bower-asset/code-prettify/loader/prettify.css') => 'css/prettify.css',
            // base_path('vendor/almasaeed2010/adminlte/bower_components/font-awesome/css/font-awesome.min.css') => 'css/font-awesome.min.css',
            // base_path('vendor/almasaeed2010/adminlte/bower_components/font-awesome/fonts') => 'fonts',
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
            // throw \Exception($src . ' does not exist as directory or file.');
        }
    }

}
