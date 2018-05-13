<?php namespace Reports\Headers;

use Illuminate\Contracts\Support\Arrayable;
use Reports\Contracts\Headers\{AltersResult, AltersHtmlResult};
use Reports\Contracts\Filters\{ForAll, ForHtml};
use Illuminate\Support\Collection;

class FilterHeader extends AbstractHeader implements AltersResult, AltersHtmlResult, Arrayable
{
    private $columns = [];
    private $filter_rules = [];

    public function rules()
    {
        return array_merge([
            'array.*.column' => 'required',
            'array.*.filter' => 'required',
            'array.*.params' => 'array'
        ], $this->filter_rules);
    }

    public function create(array $data)
    {
        foreach ($data as $index => $filter) {
            $class = 'Reports\Filters\\' . ucfirst(strtolower($filter['filter'])) . 'Filter';
            $params = isset($filter['params']) ? $filter['params'] : [];
            $obj = resolve($class)->create($params);
            $this->columns[$filter['column']][] = $obj;
            $this->addRules($index, $obj->rules());
        }
        return $this;
    }

    public function addRules(int $index, array $array)
    {
        foreach ($array as $key => $rules) {
            $this->filter_rules['array.' . $index . '.params.' . $key] = $rules;
        }
    }

    public function toArray()
    {
        return $this->columns;
    }

    public function filterHtmlResults(Collection $original, Collection $current)
    {
        return $this->filterResults($original, $current, true);
    }

    public function filterResults(Collection $original, Collection $current, $html = false)
    {
        return $current->map(function($row, $key) use ($html) {
            $new_row = $row;
            foreach ($this->columns as $column => $filters) {
                foreach ($filters as $filter) {
                    if ($filter instanceof ForAll) {
                        $new_row = $filter->filter($column, $new_row);
                        continue;
                    }
                    if ($html && $filter instanceof ForHtml) {
                        $new_row = $filter->filter($column, $new_row);
                    }
                }

            }
            return $new_row;
        });
    }

}
