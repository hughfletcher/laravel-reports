<?php namespace Reports\Headers;

use Reports\Contracts\Headers\Header;

class MetaHeader extends AbstractHeader implements Header
{

    public function rules()
    {
        return [
            'name' => 'required',
            'connection' => 'required',
            'description' => ''
        ];
    }

    // public function defaults()
    // {
    //     return [
    //         'description' => '',
    //         'cache' => false,
    //         'ignore' => false
    //     ];
    // }

}
