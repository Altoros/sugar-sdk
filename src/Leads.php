<?php

namespace Altoros\Sugar;

class Leads extends Bean
{

    protected $moduleName = 'Leads';

    protected $fields = [];

    public function __construct()
    {
        parent::__construct($this->moduleName, $this->fields);
    }

    public function create($data = [])
    {

        return parent::create($data);
    }

}
