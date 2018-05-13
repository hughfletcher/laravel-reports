<?php namespace Reports\Contracts\Headers;

use Illuminate\Support\Collection;

interface AltersResult extends Header
{
    public function filterResults(Collection $original, Collection $current);
}
