<?php namespace Reports\Responses;

use Reports\Contracts\Response;
use Reports\Report;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsxResponse implements Response
{
    public function make(Report $report)
    {
        $data = $report->run();
        $data->prepend(array_keys($data->first()));
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($data->toArray());
        (new Xlsx($spreadsheet))->save(storage_path() . '/app/test.xlsx');
        return response()->download(storage_path() . '/app/test.xlsx', 'test.xlsx')->deleteFileAfterSend(true);
    }
}
