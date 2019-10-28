<?php

namespace Altoros\Sugar;

use Altoros\Sugar\Abstracts\EntityAbstract;

class Bean extends EntityAbstract
{

    protected $moduleName;
    protected $fields;

    public function __construct(string $moduleName, array $fields = [])
    {
        $this->moduleName = $moduleName;
        $this->fields     = $fields;
    }

}
