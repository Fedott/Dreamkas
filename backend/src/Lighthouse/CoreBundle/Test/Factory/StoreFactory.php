<?php

namespace Lighthouse\CoreBundle\Test\Factory;

use Lighthouse\CoreBundle\Document\Department\Department;
use Lighthouse\CoreBundle\Document\Department\DepartmentRepository;
use Lighthouse\CoreBundle\Document\Store\Store;
use Lighthouse\CoreBundle\Document\Store\StoreRepository;
use Lighthouse\CoreBundle\Document\User\User;

class StoreFactory extends AbstractFactory
{
    const STORE_DEFAULT_NAME = '1';

    const DEFAULT_DEPARTMENT_NUMBER = '32';
    const DEFAULT_DEPARTMENT_NAME = '32';

    /**
     * @var array|Store[]
     */
    protected $stores = array();

    /**
     * @var array number => id
     */
    protected $storeNumbers = array();

    /**
     * @var User[]
     */
    protected $storeManagers = array();

    /**
     * @var User[]
     */
    protected $departmentManagers = array();

    /**
     * @var array storeId.number => id
     */
    protected $departments;

    /**
     * @param string $name
     * @param string $address
     * @param string $contacts
     * @return Store
     */
    public function createStore(
        $name = self::STORE_DEFAULT_NAME,
        $address = self::STORE_DEFAULT_NAME,
        $contacts = self::STORE_DEFAULT_NAME
    ) {
        $store = new Store();
        $store->name = $name;
        $store->address = $address;
        $store->contacts = $contacts;

        $this->getDocumentManager()->persist($store);
        $this->getDocumentManager()->flush();

        $this->storeNumbers[$name] = $store->id;

        return $store;
    }

    /**
     * @param string $name
     * @param string $address
     * @param string $contacts
     * @return string
     */
    public function getStoreId(
        $name = self::STORE_DEFAULT_NAME,
        $address = self::STORE_DEFAULT_NAME,
        $contacts = self::STORE_DEFAULT_NAME
    ) {
        return $this->getStore($name, $address, $contacts)->id;
    }

    /**
     * @param string $name
     * @param string $address
     * @param string $contacts
     * @return Store
     */
    public function getStore(
        $name = self::STORE_DEFAULT_NAME,
        $address = self::STORE_DEFAULT_NAME,
        $contacts = self::STORE_DEFAULT_NAME
    ) {
        if (!isset($this->storeNumbers[$name])) {
            $this->createStore($name, $address, $contacts);
        }
        return $this->getStoreById($this->storeNumbers[$name]);
    }

    /**
     * @param array $numbers
     * @return array number => storeId
     */
    public function getStoreIds(array $numbers)
    {
        $storeIds = array();
        foreach ($numbers as $number) {
            $storeIds[$number] = $this->getStoreId($number);
        }
        return $storeIds;
    }

    /**
     * @param array $numbers
     * @return Store[]
     */
    public function getStores(array $numbers)
    {
        $stores = array();
        foreach ($numbers as $number) {
            $stores[$number] = $this->getStore($number);
        }
        return $stores;
    }

    /**
     * @param string $storeId
     * @return Store
     * @throws \RuntimeException
     */
    public function getStoreById($storeId)
    {
        $store = $this->getStoreRepository()->find($storeId);
        if (null === $store) {
            throw new \RuntimeException(sprintf('Store id#%s not found', $storeId));
        }
        return $store;
    }

    /**
     * @return StoreRepository
     */
    protected function getStoreRepository()
    {
        return $this->container->get('lighthouse.core.document.repository.store');
    }

    /**
     * @param string $storeId
     * @param array $userIds
     * @param string $rel
     */
    public function linkManagers($storeId, $userIds, $rel)
    {
        $store = $this->getStoreById($storeId);
        $managers = $store->getManagersByRel($rel);
        foreach ((array) $userIds as $userId) {
            $user = $this->factory->user()->getUserById($userId);
            $managers->add($user);
        }
        $this->getDocumentManager()->persist($store);
        $this->getDocumentManager()->flush();
    }

    /**
     * @param array $userIds
     * @param string $storeId
     */
    public function linkStoreManagers($userIds, $storeId = null)
    {
        $storeId = ($storeId) ?: $this->getStoreId();
        $this->linkManagers($storeId, $userIds, Store::REL_STORE_MANAGERS);
    }

    /**
     * @param array $userIds
     * @param string $storeId
     */
    public function linkDepartmentManagers($userIds, $storeId = null)
    {
        $storeId = ($storeId) ?: $this->getStoreId();
        $this->linkManagers($storeId, $userIds, Store::REL_DEPARTMENT_MANAGERS);
    }

    /**
     * @param string $storeId
     * @return User
     */
    public function getStoreManager($storeId = null)
    {
        $storeId = ($storeId) ?: $this->getStoreId();
        if (!isset($this->storeManagers[$storeId])) {
            $username = 'storeManagerStore' . $storeId . '@lighthouse.pro';
            $manager = $this->factory->user()->getUser(
                $username,
                UserFactory::USER_DEFAULT_PASSWORD,
                User::ROLE_STORE_MANAGER
            );
            $this->linkStoreManagers($manager->id, $storeId);

            $this->storeManagers[$storeId] = $manager;
        }
        return $this->storeManagers[$storeId];
    }

    /**
     * @param string $storeId
     * @return User
     */
    public function getDepartmentManager($storeId = null)
    {
        $storeId = ($storeId) ?: $this->getStoreId();
        if (!isset($this->departmentManagers[$storeId])) {
            $username = 'departmentManagerStore' . $storeId . '@lighthouse.pro';
            $manager = $this->factory->user()->getUser(
                $username,
                UserFactory::USER_DEFAULT_PASSWORD,
                User::ROLE_DEPARTMENT_MANAGER
            );
            $this->linkDepartmentManagers($manager->id, $storeId);

            $this->departmentManagers[$storeId] = $manager;
        }
        return $this->departmentManagers[$storeId];
    }

    /**
     * @param Store $store
     * @param string $number
     * @param string $name
     * @return Department
     */
    public function createDepartment(Store $store = null, $number = null, $name = null)
    {
        $store = ($store) ?: $this->getStore();
        $number = ($number) ?: self::DEFAULT_DEPARTMENT_NUMBER;
        $name = ($name) ?: self::DEFAULT_DEPARTMENT_NAME;

        $department = new Department();
        $department->store = $store;
        $department->number = $number;
        $department->name = $name;

        $this->getDocumentManager()->persist($department);
        $this->getDocumentManager()->flush();

        $this->departments[$store->id . $department->number] = $department->id;

        return $department;
    }

    /**
     * @param string $departmentId
     * @return Department
     * @throws \RuntimeException
     */
    public function getDepartmentById($departmentId)
    {
        $department = $this->getDepartmentRepository()->find($departmentId);
        if (null === $department) {
            throw new \RuntimeException(sprintf('Department id#%s not found', $departmentId));
        }
        return $department;
    }

    /**
     * @param string $number
     * @param Store $store
     * @return Department
     */
    public function getDepartment($number = null, Store $store = null)
    {
        $store = ($store) ?: $this->getStore();
        $number = ($number) ?: self::DEFAULT_DEPARTMENT_NUMBER;
        if (!isset($this->departments[$store->id . $number])) {
            $this->createDepartment($store, $number);
        }
        return $this->getDepartmentById($this->departments[$store->id . $number]);
    }

    /**
     * @return DepartmentRepository
     */
    protected function getDepartmentRepository()
    {
        return $this->container->get('lighthouse.core.document.repository.department');
    }
}
