<?php namespace Reports\Contracts;

use Reports\Report;

interface Response
{
    public function make(Report $report);
}
