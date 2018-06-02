<?php namespace Reports\Filters;

use Reports\Contracts\Filters\ForHtml;

class DrilldownFilter extends AbstractFilter implements ForHtml
{

    public function filter(string $col, array $row)
    {
        $macros = [];
        foreach ($this->params['macros'] as $key => $value) {
            $macros['macros[' . $key. ']'] = $row[$value];
        }

        $new_row = $row;
        $new_row[$col] = '<a href="' . route('reports.show', array_merge(['format' => 'html', 'report' => $this->params['report']], $macros)) . '">' . $row[$col] . '</a>';
        return $new_row;
    }

    public function rules()
    {
        return [
            'report' => 'required',
            'macros' => 'array'
        ];
    }
}
