<?php namespace Reports\Contracts\Headers;

interface Header
{
    public function rules();
    public function create(array $array);
}
