@extends(config('reports.layout'))

@push('css')
    <link href="{{ asset('vendor/reports/css/jquery.treegrid.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/reports/css/reports.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ asset('vendor/reports/js/jquery.treegrid.min.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/jquery.treegrid.bootstrap3.js') }}"></script>
    <script src="{{ asset('vendor/reports/js/reports.js') }}"></script>
    <script>new ReportsList();</script>
@endpush

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Reports</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
{{-- <h1 class="page-header">Reports<small class="hidden-sm hidden-xs">Listing</small></h1> --}}
        <div id="report-table" class="table-responsive table-striped reports">
            <table id="listingTable" class="table table-bordered">
            <thead>
                <tr>
                    <td style="width:400px;">Title</td>
                    <td style="width:35%;">Description</td>
                    <td>Source</td>
                    <td>Last Modified</td>
                </tr>
            </thead>
            <tbody>
            @include('reports::html.tree', ['children' => Reports::all()])
        </tbody>
        </table>
    </div>
</div>
</div>
</div>
@endsection
