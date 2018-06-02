<?php

namespace Reports;

use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Http\Request;
use Reports\Facade as Reports;
use Gate;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ;

class Controller extends LaravelController
{

    public function report(Request $request, $type = 'html')
    {
        $report = Reports::find($request->query('report'));

        if (!Reports::check($report)) {
            throw new AccessDeniedHttpException('This action is unauthorized.');
        }

        $report->macros($request->query('macros', []));

        return response()->{$type}($report);
    }
    public function reports()
    {
        return response()->view('reports::html.reports');
    }

}
