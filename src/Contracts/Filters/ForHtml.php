<?php namespace Reports\Contracts\Filters;

interface ForHtml
{
    public function filter(string $col, array $row);
    public function create(array $array = []);
}
