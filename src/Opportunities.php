<?php


namespace Altoros\Sugar;

/**
 * Class Opportunities
 * @package Altoros\Sugar
 */
class Opportunities extends  Bean
{
    protected $moduleName = 'Opportunities';

    protected $fields = [];

    /**
     * Documents constructor.
     */
    public function __construct()
    {
        parent::__construct($this->moduleName, $this->fields);
    }
}