<?php

namespace Lighthouse\CoreBundle\MongoDB;

use Doctrine\ODM\MongoDB\UnitOfWork;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use MongoCursorInterface;
use Iterator;

/**
 * Wrapper for the PHP MongoCursor class.
 *
 * @since  1.0
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class CommandCursor implements Iterator
{
    /**
     * The MongoCursor instance being wrapped.
     *
     * @var MongoCursorInterface
     */
    protected $mongoCursor;

    /**
     * @var UnitOfWork
     */
    protected $unitOfWork;

    /**
     * @var ClassMetadata
     */
    protected $class;

    /**
     * @var array
     */
    protected $unitOfWorkHints = array();

    /**
     * @param MongoCursorInterface $mongoCursor
     * @param UnitOfWork $unitOfWork
     * @param ClassMetadata $class
     */
    public function __construct(MongoCursorInterface $mongoCursor, UnitOfWork $unitOfWork, ClassMetadata $class)
    {
        $this->mongoCursor = $mongoCursor;
        $this->unitOfWork = $unitOfWork;
        $this->class = $class;
    }

    /**
     * @param integer $num
     */
    public function batchSize($num)
    {
        $this->mongoCursor->batchSize((int) $num);
    }

    /**
     * @return array|null
     */
    public function current()
    {
        $current = $this->mongoCursor->current();
        if (null !== $current) {
            return $this->unitOfWork->getOrCreateDocument($this->class->name, $current, $this->unitOfWorkHints);
        }
    }

    /**
     * @return boolean
     */
    public function dead()
    {
        return $this->mongoCursor->dead();
    }

    /**
     * @return \MongoCursorInterface
     */
    public function getMongoCursor()
    {
        return $this->mongoCursor;
    }

    /**
     * @return array
     */
    public function info()
    {
        return $this->mongoCursor->info();
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->mongoCursor->key();
    }

    /**
     */
    public function next()
    {
        $this->mongoCursor->next();
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->mongoCursor->valid();
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->mongoCursor->rewind();
    }

    /**
     * Return the cursor's results as an array.
     *
     * If documents in the result set use BSON objects for their "_id", the
     * $useKeys parameter may be set to false to avoid errors attempting to cast
     * arrays (i.e. BSON objects) to string keys.
     *
     * @see Iterator::toArray()
     * @param boolean $useKeys
     * @return array
     */
    public function toArray($useKeys = true)
    {
        return iterator_to_array($this, $useKeys);
    }
}
