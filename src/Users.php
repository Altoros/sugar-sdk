<?php


namespace Altoros\Sugar;


class Users extends Bean
{

    protected $moduleName = 'Users';

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
