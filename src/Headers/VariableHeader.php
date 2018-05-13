<?php namespace Reports\Headers;

use Illuminate\Contracts\Support\Arrayable;
use Reports\Contracts\SupportsHtml;
use Reports\Contracts\Headers\AltersQuery;
use Illuminate\Database\Connection;
use Reports\Facade as Reports;
use Carbon\Carbon;

class VariableHeader extends AbstractHeader implements AltersQuery, SupportsHtml
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function create(array $data)
    {
        $this->data = $data;

        foreach ($data as $key => $var) {
            $array = $var;

            // modifier
            if (!isset($var['default'])) {
                if (isset($var['modifier'])) {
                    $this->data[$key]['default'] = ['modifier' => $var['modifier'][0], 'value' => null];
                } else {
                    $this->data[$key]['default'] = null;
                }
            }
        }

        return $this;
    }

    public function rules()
    {
        $rules = [
            'array.*.name' => 'string|required',
            'array.*.type' => 'string|required|in:text,date,select',
            'array.*.description' => 'string',
            'array.*.display' => 'string',
            'array.*.format' => 'string', // TODO: create valid date format rule
            'array.*.modifier' => 'array',
            'array.*.default' => '', // TODO: create type_if rule. if modifier present then array, string other wise
            'array.*.default.modifier' => 'string|required_with_all:array.*.default,array.*.modifier',
            'array.*.default.value' => 'string|required_with_all:array.*.default,array.*.modifier', //required if both default and modifier present
            'array.*.options' => 'array',
            'array.*.options.*.value' => 'string|required_with:array.*.options',
            'array.*.options.*.display' => 'string|required_with:array.*.options',
            'array.*.report_options' => 'array',
            'array.*.report_options.report' => 'string|required_with:array.*.report_options', // TODO: create report exists rule
            'array.*.report_options.macros' => 'array',
            'array.*.multiple' => 'boolean'
        ];
        return $rules;
    }


    public function process($macros)
    {

        $array = [];
        foreach ($this->data as $row) {
            if (isset($macros[$row['name']])) {

                //set modifier
                if(isset($row['modifier'])) {
                    $array['modifier_' . $row['name']] = $macros['modifier_' . $row['name']];
                }

                //set date
                if ($row['type'] == 'date' && isset($row['format'])) {
                    $array[$row['name']] = date($row['format'], strtotime($macros[$row['name']]));
                    continue;
                }

                //set daterange
                if ($row['type'] == 'daterange') {
                    $range = explode(' - ', $macros[$row['name']]);

                    if (isset($row['format'])) {
                        $array['start_' . $row['name']] = Carbon::createFromTimestamp(strtotime($range[0]))->startOfDay()->format($row['format']);
                        $array['end_' . $row['name']] = Carbon::createFromTimestamp(strtotime($range[1]))->endOfDay()->format($row['format']);
                    } else {
                        $array['start_' . $row['name']] = $range[0];
                        $array['end_' . $row['name']] = $range[1];
                    }

                    continue;
                }

                $array[$row['name']] = $macros[$row['name']];

                continue;
            } elseif(isset($row['default'])) {

                if (isset($row['modifier']) && is_array($row['modifier'])) {
                    $array[$row['name']] = $row['default']['value'];
                    $array['modifier_' . $row['name']] = $row['default']['modifier'];
                } else {
                    $array[$row['name']] = $row['default'];
                }


            } else {
                $array[$row['name']] = null;
            }

        }
        
        return $array;
    }

    public function provide()
    {
        foreach ($this->data as $key => $var) {

            //if report options specified in a select variable
            if ($var['type'] == 'select' && isset($var['report_options'])) {
                $options = [];
                $report = Reports::find($var['report_options']['report']);
                if(isset($var['report_options']['macros'])) {
                    $report = $report->macros($var['report_options']['macros']);
                }
                $result = $report->run();
                foreach ($result as $row) {
                    $options[] = ['value' => $row[$var['report_options']['value']], 'display' => $row[$var['report_options']['display']]];
                }
                $this->data[$key]['options'] = $options;

            }
        }
    }

    public function toArray()
    {
        return $this->data;
    }


}
