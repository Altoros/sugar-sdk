<?php


namespace Altoros\Sugar;

/**
 * Upload documents in sugar
 *
 * Class Documents
 * @package App\Ext\Sugar
 */
class Documents extends Bean
{

    protected $moduleName = 'Documents';

    protected $fields = [];

    /**
     * Documents constructor.
     */
    public function __construct()
    {
        parent::__construct($this->moduleName, $this->fields);
    }

}
