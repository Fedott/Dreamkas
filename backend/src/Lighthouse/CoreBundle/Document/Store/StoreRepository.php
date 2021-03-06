<?php

namespace Lighthouse\CoreBundle\Document\Store;

use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\LockMode;
use Lighthouse\CoreBundle\Document\DocumentRepository;

/**
 * @method Store find($id, $lockMode = LockMode::NONE, $lockVersion = null)
 * @method Store[]|Cursor findAll
 * @method Store findOneBy(array $criteria, array $sort = array(), array $hints = array())
 */
class StoreRepository extends DocumentRepository
{
    /**
     * @return string[]
     */
    public function findAllStoresManagerIds()
    {
        return $this->findAllManagerIds(Store::REL_STORE_MANAGERS);
    }

    /**
     * @return string[]
     */
    public function findAllDepartmentManagerIds()
    {
        return $this->findAllManagerIds(Store::REL_DEPARTMENT_MANAGERS);
    }

    /**
     * @param string $rel
     * @return string[]
     */
    public function findAllManagerIds($rel)
    {
        $query = $this
            ->createQueryBuilder()
            ->hydrate(false)
            ->select($rel)
            ->getQuery();
        $result = $query->execute();
        $userIds = array();
        foreach ($result as $row) {
            if (isset($row[$rel]) && is_array($row[$rel])) {
                foreach ($row[$rel] as $manager) {
                    $userIds[] = (string) $manager;
                }
            }
        }
        return $userIds;
    }

    /**
     * @param string $userId
     * @return mixed
     */
    public function findByManagers($userId)
    {
        $query = $this->createQueryBuilder();
        $query->addOr($query->expr()->field('departmentManagers')->equals($userId)->getQuery());
        $query->addOr($query->expr()->field('storeManagers')->equals($userId)->getQuery());

        return $query->getQuery()->execute();
    }

    /**
     * @param string $address
     * @return Store
     */
    public function findOneByAddress($address)
    {
        return $this->findOneBy(array('address' => $address));
    }

    /**
     * @param $name
     * @return Store
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(array('name' => $name));
    }

    /**
     * @return Cursor|Store[]
     */
    public function findAllActive()
    {
        return $this->findBy(
            array('deletedAt' => null),
            array('id' => self::SORT_ASC)
        );
    }
}
