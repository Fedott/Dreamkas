<?php

namespace Lighthouse\CoreBundle\Document;

use Doctrine\MongoDB\ArrayIterator;
use Doctrine\MongoDB\Exception\ResultException;
use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\DocumentRepository as BaseRepository;
use Doctrine\ODM\MongoDB\LockMode;
use Doctrine\ODM\MongoDB\Proxy\Proxy;
use Doctrine\MongoDB\Collection;
use Doctrine\ODM\MongoDB\Types\Type;
use Lighthouse\CoreBundle\MongoDB\CommandCursor;
use Lighthouse\CoreBundle\MongoDB\DocumentManager;
use Lighthouse\CoreBundle\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as BaseClassMetadata;
use MongoCollection;
use MongoId;

/**
 * @method Cursor findAll()
 * @method DocumentManager getDocumentManager()
 */
class DocumentRepository extends BaseRepository
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    /**
     * @return AbstractDocument
     */
    public function createNew()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }

    /**
     * @param $document
     */
    public function save($document)
    {
        $this->getDocumentManager()->persist($document);
        $this->getDocumentManager()->flush($document);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->createQueryBuilder()->getQuery()->count();
    }

    /**
     * @return array
     */
    public function findAllIds()
    {
        $qb = $this->createQueryBuilder()
            ->hydrate(false)
            ->select('_id');
        $result = $qb->getQuery()->execute();
        $ids = array();
        foreach ($result as $row) {
            $ids[] = $row['_id'];
        }
        return $ids;
    }

    /**
     * @param array $criteria
     * @param array $sort
     * @param array $hints
     * @return null|object
     */
    public function findOneBy(array $criteria, array $sort = array(), array $hints = array())
    {
        return $this->uow->getDocumentPersister($this->documentName)->load(
            $criteria,
            null,
            $hints,
            LockMode::NONE,
            $sort
        );
    }

    /**
     * @param array $criteria
     * @param array $sort
     * @param int $limit
     * @param int $skip
     * @return \Doctrine\ODM\MongoDB\Cursor
     */
    public function findBy(array $criteria, array $sort = null, $limit = null, $skip = null)
    {
        return $this->getDocumentPersister()->loadAll($criteria, $sort, $limit, $skip);
    }

    /**
     * @return bool
     */
    public function isCollectionEmpty()
    {
        return 0 == $this->count();
    }

    /**
     * @param string $id
     * @return Proxy
     */
    public function getReference($id)
    {
        return $this->getDocumentManager()->getReference($this->getClassName(), $id);
    }

    /**
     * @param string $id
     * @throws \RuntimeException
     * @return NullObjectInterface
     */
    public function getNullObject($id)
    {
        throw new \RuntimeException('Null Object method is not implemented');
    }

    /**
     * @return Collection
     */
    protected function getDocumentCollection()
    {
        return $this->getDocumentManager()->getDocumentCollection($this->getClassName());
    }

    /**
     * @return MongoCollection
     */
    protected function getMongoCollection()
    {
        return $this->getDocumentCollection()->getMongoCollection();
    }

    /**
     * @param array $pipeline
     * @return ArrayIterator
     * @throws ResultException
     */
    protected function aggregate(array $pipeline)
    {
        $result = $this->getDocumentCollection()->getMongoCollection()->aggregate($pipeline);

        if (empty($result['ok'])) {
            throw new ResultException($result);
        }

        $arrayIterator = new ArrayIterator(isset($result['result']) ? $result['result'] : array());
        $arrayIterator->setCommandResult($result);

        return $arrayIterator;
    }

    /**
     * @param array $pipeline
     * @param BaseClassMetadata $class
     * @param array $options
     * @return CommandCursor
     */
    protected function aggregateCursor(array $pipeline, BaseClassMetadata $class, array $options = array())
    {
        /* @var \MongoCursorInterface $cursor */
        $mongoCursor = $this->getDocumentCollection()->getMongoCollection()->aggregateCursor($pipeline, $options);
        return new CommandCursor($mongoCursor, $this->dm->getUnitOfWork(), $class);
    }

    /**
     * @param array $ids
     * @return MongoId[]
     */
    protected function convertToMongoIds(array $ids)
    {
        return array_map(
            function ($id) {
                return new MongoId((string) $id);
            },
            $ids
        );
    }

    /**
     * @param array $values
     * @param string $typeName
     * @return array
     */
    protected function convertToType(array $values, $typeName)
    {
        $type = Type::getType($typeName);
        return array_map(
            function ($value) use ($type) {
                return $type->convertToDatabaseValue($value);
            },
            $values
        );
    }

    /**
     * @param object $document
     * @return string
     */
    public function getDocumentIdentifierValue($document)
    {
        return $this->dm->getClassMetadata(get_class($document))->getIdentifierValue($document);
    }
}
