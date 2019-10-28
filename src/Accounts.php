<?php

namespace Altoros\Sugar;

class Accounts extends Bean
{

    protected $moduleName = 'Accounts';

    protected $fields = [];

    public function __construct()
    {
        parent::__construct($this->moduleName, $this->fields);
    }

    public function create($data = [])
    {
        $data['si_group_status_c'] = $data['si_group_status_c'] ?? 'New';

        return parent::create($data);
    }


    /**
     * @param string $accountName
     * @param array  $fields
     *
     * @return array
     * @throws Exceptions\SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function findSimilarByName(string $accountName, $fields = [])
    {
        return $this->findBy([
            '$or' => [
                ['name' => ['$contains' => $accountName]],
                ['website' => ['$contains' => $accountName]],
            ],
        ], $fields);
    }

    /**
     * @param string $accountName
     * @param array  $fields
     *
     * @return array
     * @throws Exceptions\SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function findByName(string $accountName, $fields = [])
    {
        return $this->findBy(['name' => $accountName], $fields);
    }
}
