<?php namespace Reports\Contracts\Headers;

use Illuminate\Support\Collection;

interface AltersHtmlResult extends Header
{
    public function filterHtmlResults(Collection $original, Collection $current);
}
