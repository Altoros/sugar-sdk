<?php

namespace Altoros\Sugar\Abstracts;


use Altoros\Sugar\Exceptions\SugarException;
use Altoros\Sugar\SugarSDK;
use SugarAPI\SDK\SugarAPI;

abstract class EntityAbstract
{

    protected $moduleName = '';

    protected $fields = [];


    /**
     * CRUD Methods
     */

    /**
     * @param array $data
     *
     * @return mixed
     * @throws SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function create($data = [])
    {
        $response = $this->getSugar()->createRecord($this->moduleName)->execute($data)->getResponse();

        if ($response->getStatus() !== 200) {
            throw new SugarException($response->getBody());
        }

        $result = $response->getBody(false);

        if (empty($result->id)) {
            throw new SugarException($response->getError());
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function update($data = [])
    {
        if (!isset($data['id']) || empty($data['id'])) {
            return $this->create($data);
        }

        $id = $data['id'];
        unset($data['id']);

        $response = $this->getSugar()->updateRecord($this->moduleName, $id)->execute($data)->getResponse();

        if ($response->getStatus() !== 200) {
            throw new SugarException($response->getBody());
        }

        $result = $response->getBody(false);

        if (empty($result->id)) {
            throw new SugarException($result->getError());
        }

        return $result;
    }

    /**
     * @param       $id
     * @param array $data
     *
     * @return string
     * @throws SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function getRecord($id, $data = [])
    {
        $response = $this->getSugar()->getRecord($this->moduleName, $id)->execute($data)->getResponse();
        if ($response->getStatus() !== 200) {
            throw new SugarException($response->getBody());
        }

        return $response->getBody(false);
    }

    /**
     * @param array $data
     *
     * @return mixed
     * @throws SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function filterRecords(array $data = [])
    {
        $data['max_num'] = $data['max_num'] ?? '-1';

        if (array_keys($data['filter']) !== range(0, count($data['filter']) - 1)) {
            $data['filter'] = [$data['filter']];
        }

        $response = $this->getSugar()->filterRecords($this->moduleName)->execute($data)->getResponse();

        if ($response->getStatus() !== 200) {
            throw new SugarException($response->getBody());
        }

        $recordList = $response->getBody(false);

        return $recordList->records;
    }

    /**
     * todo
     */
    public function delete()
    {

    }


    /**
     * Relationships
     */

    /**
     * @param string $record_id
     * @param string $relationship
     * @param array $relatedData
     *
     * @return \StdClass Related object
     * @throws SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function createRelated(string $record_id, string $relationship, array $relatedData): \StdClass
    {
        $response = $this->getSugar()->createRelated(
            $this->moduleName,
            $record_id,
            $relationship
        )->execute($relatedData)->getResponse();

        if ($response->getStatus() !== 200) {
            throw new SugarException($response->getBody());
        }

        $result = $response->getBody(false);

        if (empty($result->related_record)) {
            throw new SugarException($response->getError());
        }

        return $result->related_record;
    }

    /**
     * @param string $id
     * @param string $relatedModule
     * @param array $data
     *
     * @return array
     * @throws SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function filterRelated(string $id, string $relatedModule, array $data = []): array
    {
        $builder = $this->getSugar()->filterRelated($this->moduleName, $id, $relatedModule);

        /** if associative array */
        if (isset($data['filter'])) {
            if (array_keys($data['filter']) !== range(0, count($data['filter']) - 1)) {
                $data['filter'] = [$data['filter']];
            }
        }

        $data['max_num'] = $data['max_num'] ?? '100';

        $records = [];
        $it = 0;
        do {
            $it++;
            $data['offset'] = $recordList->next_offset ?? 0;
            $response = $builder->execute($data)->getResponse();

            if ($response->getStatus() === 401) {
                $this->getSugar(true);
                $response = $builder->execute($data)->getResponse();
            }

            if ($response->getStatus() !== 200) {
                throw new SugarException($response->getBody());
            }

            $recordList = $response->getBody(false);

            $records = array_merge($records, $recordList->records);
        } while ($recordList->next_offset !== -1 && $it < 100);


        return $records;
    }

    /**
     * @param string $id
     * @param string $relatedModule
     * @param string $relatedId
     *
     * @throws SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function linkRecords(string $id, string $relatedModule, string $relatedId): void
    {
        $builder = $this->getSugar()->linkRecords($this->moduleName, $id, $relatedModule, $relatedId);

        $response = $builder->execute()->getResponse();

        if ($response->getStatus() === 401) {
            $this->getSugar(true);
            $response = $builder->execute()->getResponse();
        }

        if ($response->getStatus() !== 200) {
            throw new SugarException($response->getBody());
        }
    }


    /**
     * Custom methods
     */

    /**
     * @param array $filter
     * @param array $fields
     * @param string $max
     *
     * @return array
     * @throws SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function findBy(array $filter, $fields = [], string $max = '-1'): array
    {
        if (empty($filter)) {
            return [];
        }

        /** if associative array */
        if (array_keys($filter) !== range(0, count($filter) - 1)) {
            $filter = [$filter];
        }

        $data = [
            'filter' => $filter,
            'fields' => $fields,
            'max_num' => $max,
        ];

        $builder = $this->getSugar()->filterRecords($this->moduleName);

        $data['max_num'] = $data['max_num'] ?? '100';

        $records = [];
        $it = 0;
        do {
            $it++;
            $data['offset'] = $recordList->next_offset ?? 0;

            $response = $builder->execute($data)->getResponse();

            if ($response->getStatus() === 401) {
                $this->getSugar(true);
                $response = $builder->execute($data)->getResponse();
            }

            if ($response->getStatus() !== 200) {
                throw new SugarException($response->getBody());
            }

            $recordList = $response->getBody(false);

            $records = array_merge($records, $recordList->records);

        } while ($recordList->next_offset !== -1 && $it < 100);

        return $records;
    }


    /**
     * @param string $recordId
     * @param string $filePath
     * @return string
     * @throws SugarException
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    public function attachFile(string $recordId, string $filePath)
    {
        $builder = $this->getSugar()->attachFile($this->moduleName, $recordId, 'filename');

        $response = $builder->execute($filePath)->getResponse();
        if ($response->getStatus() === 401) {
            $this->getSugar(true);
            $response = $builder->execute($filePath)->getResponse();
        }

        if ($response->getStatus() !== 200) {
            throw new SugarException($response->getBody());
        }

        $result = $response->getBody(false);

        if (empty($result->record)) {
            throw new SugarException($response->getError());
        }

        return $result->record;
    }

    /**
     * Service methods
     */

    /**
     * @param bool $update
     *
     * @return SugarAPI
     * @throws \SugarAPI\SDK\Exception\Authentication\AuthenticationException
     * @throws \SugarAPI\SDK\Exception\SDKException
     */
    protected function getSugar(bool $update = false): SugarAPI
    {
        $sugar = SugarSDK::getInstance($update);

        return $sugar;
    }
}
