<?php namespace Reports\Headers;

abstract class AbstractHeader
{
    protected $data;

    public function create(array $data)
    {
        $this->data = $data;
        return $this;
    }

}
