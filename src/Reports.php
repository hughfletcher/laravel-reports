<?php namespace Reports;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Application;

class Reports
{
    private $reports;
    private $fs;
    private $db;

    public function __construct(Application $app)
    {
        $this->fs = $app['filesystem']->disk(config('reports.reports.disk'));
        $this->db= $app['db'];
    }

    public function all()
    {
        return $this->allRecursive(config('reports.reports.path'));
    }

    // public function find($path, $macros = [])
    // {
    //     $report = $this->findOrCreate($path);
    //     $report->macros($macros);
    //
    //     return $report;
    // }

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
            // $result->push(new Report($this->fs, $this->db, $file));
            $result->push(app()->makeWith('Reports\Report', ['path' => $file]));
        }

        foreach($this->fs->directories($path) as $dir) {

            $result->push(new Directory($this, $dir));

        }

        if ($path == config('reports.reports.path') && !$this->reports) {
            $this->reports = $result;
        }

        return $result;
    }

}
