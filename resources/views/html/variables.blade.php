<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#variables" aria-expanded="true" aria-controls="variables">
                    Report Configuration
                </a>
            </h4>
        </div>
        <div id="variables" class="panel-collapse collapse{{ !$report->ready ? ' in' : null }}" role="tabpanel" aria-labelledby="headingOne">
            <form action="{{ route('reports.report', ['type' => 'html']) }}" class="form-horizontal" method="get">
                <input type="hidden" name="report" value="{{ $report->report_path }}" />
                <div class="panel-body">
                @foreach ($report->headers['variables']->toArray() as $variable)
                    <div class="form-group">
                        <label for="macros[{{ $variable['name'] }}]" class="col-sm-2 control-label">{{ $variable['display'] or $variable['name'] }}</label>

                        @if(isset($variable['modifier']))
                        <div class="row">
                            <div class="col-xs-1">
                                <select class="form-control input-sm" name="macros[modifier_{{ $variable['name'] }}]">
                                  @foreach ($variable['modifier'] as $modifier)
                                      <option{{ (isset(request()->query('macros')['modifier_' . $variable['name']]) ? (request()->query('macros')['modifier_' . $variable['name']]) : $variable['default']['modifier']) == $modifier ? ' selected' : '' }}>{{ $modifier }}</option>
                                  @endforeach
                                </select>
                            </div>
                        @endif
                        @if(in_array($variable['type'], ['text', 'date', 'daterange']))
                            <div class="col-xs-{{ isset($variable['modifier']) ? '2' : '3' }}">
                                <input type="text" name="macros[{{ $variable['name'] }}]" class="form-control input-sm {{ $variable['type'] }}" value="{{ isset(request()->query('macros')[$variable['name']]) ? (request()->query('macros')[$variable['name']]) : (isset($variable['modifier']) ? $variable['default']['value'] : $variable['default']) }}">
                            </div>
                        @elseif ($variable['type'] == 'select')
                            <div class="col-xs-3">
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
                <div class="panel-footer"><input class="btn btn-primary btn-sm" type="submit" value="Run Report"></div>
            </form>
        </div>
    </div>
</div>
