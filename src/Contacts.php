<?php

namespace Altoros\Sugar;

class Contacts extends Bean
{

    protected $moduleName = 'Contacts';

    protected $fields = [];

    public function __construct()
    {
        parent::__construct($this->moduleName, $this->fields);
    }

    public function create($data = [])
    {
        $data['status_c'] = $data['status_c'] ?? 'New';

        return parent::create($data);
    }

}
