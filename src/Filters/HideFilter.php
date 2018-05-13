<?php namespace Reports\Filters;

use Reports\Contracts\Filters\ForAll;
use Illuminate\Support\Collection;

class HideFilter extends AbstractFilter implements ForAll
{

    public function filter(string $col, array $row)
    {
        $new_row = $row;
        unset($new_row[$col]);
        return $new_row;
    }

    public function rules()
    {
        return [];
    }
}
