<div id="config-modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="config-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('reports.show', ['type' => 'html']) }}" class="form-horizontal" method="get">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Report Configuration</h4>
            </div>
            <div class="modal-body">

                <input type="hidden" name="report" value="{{ $report->report_path }}" />
                <div class="panel-body">
                @foreach ($report->headers['variables']->toArray() as $variable)
                    <div class="form-group">
                        <label for="macros[{{ $variable['name'] }}]" class="col-sm-2 control-label">{{ $variable['display'] or $variable['name'] }}</label>

                        @if(isset($variable['modifier']))
                        <div class="row">
                            <div class="col-xs-2">
                                <select class="form-control input-sm" name="macros[modifier_{{ $variable['name'] }}]">
                                  @foreach ($variable['modifier'] as $modifier)
                                      <option{{ (isset(request()->query('macros')['modifier_' . $variable['name']]) ? (request()->query('macros')['modifier_' . $variable['name']]) : $variable['default']['modifier']) == $modifier ? ' selected' : '' }}>{{ $modifier }}</option>
                                  @endforeach
                                </select>
                            </div>
                        @endif
                        @if(in_array($variable['type'], ['text', 'date', 'daterange']))
                            <div class="col-xs-{{ isset($variable['modifier']) ? '7' : '10' }}">
                                <input type="text" name="macros[{{ $variable['name'] }}]" class="form-control input-sm {{ $variable['type'] }}" value="{{ isset(request()->query('macros')[$variable['name']]) ? (request()->query('macros')[$variable['name']]) : (isset($variable['modifier']) ? $variable['default']['value'] : $variable['default']) }}">
                            </div>
                        @elseif ($variable['type'] == 'select')
                            <div class="col-xs-10">
                                <select class="form-control input-sm" name="macros[{{ $variable['name'] }}]">
                                  @foreach ($variable['options'] as $option)
                                    <option value="{{ $option['value'] }}" {{ (isset(request()->query('macros')[$variable['name']]) ? (request()->query('macros')[$variable['name']]) : $variable['default']) == $option['value'] ? ' selected' : '' }}>
                                        {{ $option['display'] }}
                                    </option>
                                  @endforeach
                                </select>
                            </div>
                        @endif
                        @if(isset($variable['modifier']))
                        </div>
                        @endif


                        {{-- <div class="col-xs-3">
                            <input name="macros[{{ $variable['name'] }}]" type="{{ $variable['type'] }}" class="form-control input-sm" value="{{ isset(request()->query('macros')[$variable['name']]) ? (request()->query('macros')[$variable['name']]) : $variable['default'] }}">
                        </div> --}}


                    </div>
                 @endforeach


                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Run Report</button>
            </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
