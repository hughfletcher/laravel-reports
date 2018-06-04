@extends(config('reports.layout'))

@push('css')

    <link href="{{ asset('vendor/reports/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/reports/css/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/reports/css/prettify.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/reports/css/reports.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ asset('vendor/reports/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/jquery.stickytableheaders.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/prettify.js') }}"></script>
    <script>window.Report = {!! json_encode($report->config()) !!}</script>
    <script src="{{ asset('vendor/reports/js/reports.js') }}"></script>
    <script>new ReportsShow();</script>
@endpush

@section('content-title')
    <h1 class="page-header">{{ !is_null($report->name) ? $report->name : $report->report_path }}<small class="hidden-sm hidden-xs">{{ $report->description }}</small></h1>
@endsection

@section('content')

{{-- @includeWhen((isset($report->headers['variables'])), 'reports::html.variables') --}}

{{--  --}}

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    @if(isset($report->headers['variables']))
                    <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#config-modal">Config</button>
                    @endif
                    <div class="btn-group">
                        <button type="button" class="btn dropdown-toggle btn-sm btn-default disabled" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Show <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('reports.show', ['format' => 'csv', 'report' => $report->report_path, 'macros' => $report->macros]) }}">CSV</a></li>
                            <li><a href="{{ route('reports.show', ['format' => 'xlsx', 'report' => $report->report_path, 'macros' => $report->macros]) }}">Download Excel</a></li>
                            @if(class_exists('dekor\ArrayToTextTable'))<li><a href="{{ route('reports.show', ['format' => 'text', 'report' => $report->report_path, 'macros' => $report->macros]) }}">Text</a></li>@endif
                            <li role="separator" class="divider"></li>
                            <li><a href="{{ route('reports.show', ['format' => 'json', 'report' => $report->report_path, 'macros' => $report->macros]) }}">JSON</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-sm btn-default disabled" data-toggle="modal" data-target="#query-modal">Query</button>
                </div>
            </div>
          <!-- /.box-header -->
            <div class="box-body">
                <div class="alert alert-{{ $report->header_errors || $report->macro_errors ? 'danger' : 'info' }}{{ $report->ready ? ' hide' : '' }}" id="need-info">
                    @if ($report->header_errors)
                    <h4>There are issues with your file configuration.</h4>
                        @foreach ($report->header_errors as $reporter => $error)
                            <dl> <dt>{{ $reporter }}</dt>
                            @foreach ($error->all() as $message)
                                <dd>{{ $message }}</dd>
                            @endforeach
                            </dl>
                        @endforeach
                    @elseif($report->macro_errors)
                        <h4>There are issues with your report configuration.</h4>
                        <ul>
                        @foreach ($report->macro_errors->all() as $error)
                                <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    @else
                    This report needs more information before running.
                    @endif
                </div>
                <div class='alert alert-block alert-info' id='async-notice' style='text-align: center;{{ !$report->ready ? 'display: none;' : null }}'>
                    <div>Your report is running.</div>
                    <div class="progress progress-striped active" style="width: 50%; margin: 20px auto 0;">
                        <div class="progress-bar"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">Working</span>
                        </div>
                    </div>
                </div>
                <div id="report-table" class="table-responsive table-striped reports"></div>
            </div>
        </div>
    </div>
</div>
@includeWhen((isset($report->headers['variables'])), 'reports::html.variables')
<div class="modal" id="query-modal" tabindex="-1" role="dialog" aria-labelledby="query-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Report Query</h4>
            </div>
            <div class="modal-body">
                <div id='query_holder'></div>
            </div>
    </div>
  </div>
</div>
@endsection
