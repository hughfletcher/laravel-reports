<?php namespace Reports\Filters;

use Reports\Contracts\FilterContract;

abstract class AbstractFilter
{
    protected $params;

    public function create(array $array = [])
    {
        $this->params = $array;
        return $this;
    }
}
