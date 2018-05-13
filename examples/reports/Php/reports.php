<?php
// {
//     "name": "Reports",
//     "description": "Lists all reports.",
//     "ignore": true,
//     "connection": "php"
// }


function all_reports_to_array($reports)
{
    $array = [];
    foreach ($reports as $key => $item) {
        if ($item instanceof Reports\Report) {
            $array[] = [
                'name' => $item->name,
                'description' => $item->description,
                'children' => null
            ];
        } elseif ($item instanceof Reports\Directory) {
            $array[] = [
                'name' => $item->name,
                'description' => $item->description,
                'children' => all_reports_to_array($item->children)
            ];
        }
    }
    return $array;

}

return all_reports_to_array(Reports::all());
