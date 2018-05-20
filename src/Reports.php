<?php namespace Reports;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;
use Closure;

class Reports
{
    private $reports;
    private $fs;
    private $authorize;

    public function __construct(Application $app)
    {
        $this->fs = $app['filesystem']->disk(config('reports.reports.disk'));
    }

    public function all()
    {
        return $this->allRecursive(config('reports.reports.path'));
    }

    public function find($search, $items = null)
    {

        if (!$items) {
            $path = config('reports.reports.path') . '/' . $search;
            return app()->makeWith('Reports\Report', ['path' => $path]);
        }

        foreach ($items as $item) {

            if ($item instanceof Report && $item->path == $search) {
                return $item;
            } elseif ($item instanceof Directory) {
                if ($report = self::find($search, $item->children)) {
                    return $report;
                }
            }

        }

    }

    public function allRecursive($path)
    {
        if ($path == config('reports.reports.path') && $this->reports) {
            return $this->reports;
        }

        $result = collect();
        foreach ($this->fs->files($path) as $file) {
            $report = app()->makeWith('Reports\Report', ['path' => $file]);
            if ($this->check($report)) {
                $result->push($report);
            }

        }

        foreach($this->fs->directories($path) as $dir) {

            $dir = app()->makeWith('Reports\Directory', ['reports' => $this, 'path' => $dir]);

            if ($dir->children->isNotEmpty()) {
                $result->push($dir);
            }

        }

        if ($path == config('reports.reports.path') && !$this->reports) {
            $this->reports = $result;
        }

        return $result;
    }


    // Reports::auth(function($report) {
    //     return request()->query('user') == $report->auth;
    // });

    public function auth(Closure $callback)
    {
        $this->authUsing = $callback;
    }

    public function check($report)
    {
        return call_user_func($this->authUsing, $report);
    }

}
