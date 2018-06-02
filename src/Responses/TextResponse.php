<?php namespace Reports\Responses;

use Reports\Contracts\Response;
use Reports\Report;
use dekor\ArrayToTextTable;
use Exception;

class TextResponse extends AbstractResponse implements Response
{
    public function make(Report $report)
    {
        if (!$report->ready) {
            return $report->toArray()['message'];
        }

        if (!class_exists('dekor\ArrayToTextTable')) {
            throw new Exception('dekor\ArrayToTextTable class required and can not be found.');
        }
        return response((new ArrayToTextTable($this->filter($report)->toArray()))->render(), 200)
            ->header('Content-Type', 'text/plain');
    }
}
