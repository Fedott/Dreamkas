<?php

namespace Lighthouse\CoreBundle\Tests\Document;

use Lighthouse\CoreBundle\Test\TestCase;
use Lighthouse\CoreBundle\Tests\Fixtures\Document\Test;

class AbstractDocumentTest extends TestCase
{
    public function testGetSetProperties()
    {
        $document = new Test();

        $document->name = 'name';
        $this->assertEquals('name', $document->name);

        $document->desc = 'info';
        $this->assertEquals('info', $document->desc);

        $this->assertNull($document->id);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidPropertyGet()
    {
        $product = new Test();

        $product->invalid;
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidPropertySet()
    {
        $document = new Test();

        $document->invalid = 'invalid';
    }

    public function testPopulate()
    {
        $array = array(
            'id' => 1,
            'name' => 'Кефир "Веселый Молочник" 1% 950гр',
            'desc' => 'Классный кефирчик, употребляю давно, всем рекомендую для поднятия тонуса',
        );

        $document = new Test();
        $document->populate($array);

        foreach ($array as $key => $value) {
            $this->assertEquals($value, $document->$key);
        }
    }
}
