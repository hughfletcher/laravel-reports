<?php namespace Reports\Contracts\Headers;

interface AltersQuery extends Header
{
    public function process($macros);

}
