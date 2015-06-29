<?php

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class MongoWriteConcernException extends MongoCursorException
{
    /**
     * Get the error document
     * @return array
     */
    public function getDocument()
    {
    }

    /**
     * The hostname of the server that encountered the error
     * @return string
     */
    public function getHost()
    {
    }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class MongoDuplicateKeyException extends MongoWriteConcernException
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
interface MongoCursorInterface extends Iterator
{

    /**
     * @param int $number
     */
    public function batchSize($number);

    /**
     * @return array Can return any type.
     */
    public function current();

    /**
     * @return void
     */
    public function next();

    /**
     * @return string
     */
    public function key();

    /**
     * @return boolean
     */
    public function valid();

    /**
     * @return void
     */
    public function rewind();

    /**
     * @return array
     */
    public function info();

    /**
     * @return bool
     */
    public function dead();
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class MongoCommandCursor implements MongoCursorInterface
{
    /**
     * @param MongoClient $connection
     * @param string $connection_hash
     * @param $cursor_document
     * @return MongoCommandCursor
     */
    public static function createFromDocument(MongoClient $connection, $connection_hash, $cursor_document)
    {
    }

    /**
     * @param MongoClient $connection Database connection
     * @param string $ns Full name of database and collection
     * @param array $query Database query
     */
    public function __construct(MongoClient $connection, $ns, $query = array())
    {
    }

    /**
     * @param int $number
     */
    public function batchSize($number)
    {
    }

    /**
     * @return array Can return any type.
     */
    public function current()
    {
    }

    /**
     * @return void
     */
    public function next()
    {
    }

    /**
     * @return string
     */
    public function key()
    {
    }

    /**
     * @return boolean
     */
    public function valid()
    {
    }

    /**
     * @return void
     */
    public function rewind()
    {
    }

    /**
     * @return array
     */
    public function info()
    {
    }

    /**
     * @return bool
     */
    public function dead()
    {
    }
}