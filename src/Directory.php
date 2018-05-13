<?php namespace Reports;

use Illuminate\Contracts\Support\Arrayable;
use Storage;

class Directory implements Arrayable
{
    private $data;

    public function __construct(Reports $reports, $path)
    {
        $children = $reports->allRecursive($path);

        $this->data = array_merge([
            'name' => ucwords(pathinfo($path, PATHINFO_BASENAME)),
            'id'=> substr(md5($path), 0, 6),
            'children' => $children,
            'count' => $children->count(),
            'path' => $path //debug
        ], $this->getMeta($path));
    }

    protected function getMeta($path)
    {
        if (self::disk()->exists($path . '/' . 'meta.json')) {
            return json_decode(self::disk()->get($path . '/' . 'meta.json'));
        }
        return ['title' => null, 'description' => null];
    }

    public static function disk()
    {
        return Storage::disk(config('reports.disk.reports'));
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function toArray()
    {
        return array_merge(
            $this->data,
            [
                'children' => $this->data['children']->toArray(),
                'is_dir' => true
            ]);
    }

}
