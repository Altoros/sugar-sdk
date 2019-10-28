<?php


namespace Altoros\Sugar;


class Notes extends Bean
{

    protected $moduleName = 'Notes';

    protected $fields = [];

    /**
     * Documents constructor.
     */
    public function __construct()
    {
        parent::__construct($this->moduleName, $this->fields);
    }

}
