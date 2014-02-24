<?php

namespace Lighthouse\CoreBundle\Tests\Controller;

use Lighthouse\CoreBundle\Document\User\User;
use Lighthouse\CoreBundle\Test\Assert;
use Lighthouse\CoreBundle\Test\WebTestCase;

class SupplierControllerTest extends WebTestCase
{
    public function testPost()
    {
        $accessToken = $this->factory->authAsRole(User::ROLE_COMMERCIAL_MANAGER);
        $postData = array(
            'name' => 'ООО "ЕвроАрт"'
        );
        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/suppliers',
            $postData
        );
        $this->assertResponseCode(201);
        Assert::assertJsonHasPath('id', $postResponse);
        Assert::assertJsonPathEquals($postData['name'], 'name', $postResponse);
    }

    /**
     * @dataProvider postValidationProvider
     * @param array $postData
     * @param $expectedResponseCode
     * @param array $assertions
     */
    public function testPostValidation(array $postData, $expectedResponseCode, array $assertions)
    {
        $accessToken = $this->factory->authAsRole(User::ROLE_COMMERCIAL_MANAGER);
        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/suppliers',
            $postData
        );
        $this->assertResponseCode($expectedResponseCode);

        $this->performJsonAssertions($postResponse, $assertions);
    }

    /**
     * @return array
     */
    public function postValidationProvider()
    {
        return array(
            'name valid length 100' => array(
                array(
                    'name' => str_repeat('z', 100),
                ),
                201,
                array(
                )
            ),
            'name invalid length 101' => array(
                array(
                    'name' => str_repeat('z', 101),
                ),
                400,
                array(
                    'children.name.errors.0' => 'Не более 100 символов',
                    'children.name.errors.1' => null
                )
            ),
            'name empty' => array(
                array(
                    'name' => '',
                ),
                400,
                array(
                    'children.name.errors.0' => 'Заполните это поле',
                    'children.name.errors.1' => null
                )
            )
        );
    }

    /**
     * @dataProvider postDuplicateNameProvider
     * @param string $firstName
     * @param string $secondName
     */
    public function testPostDuplicateName($firstName, $secondName)
    {
        $accessToken = $this->factory->authAsRole(User::ROLE_COMMERCIAL_MANAGER);
        $postData = array(
            'name' => $firstName
        );
        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/suppliers',
            $postData
        );
        $this->assertResponseCode(201);
        Assert::assertJsonPathEquals($postData['name'], 'name', $postResponse);

        $postData = array(
            'name' => $secondName
        );
        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/suppliers',
            $postData
        );
        $this->assertResponseCode(400);
        Assert::assertJsonPathEquals(
            'Поставщик с таким названием уже существует',
            'children.name.errors.0',
            $postResponse
        );
        Assert::assertNotJsonHasPath('children.name.errors.1', $postResponse);
    }

    /**
     * @return array
     */
    public function postDuplicateNameProvider()
    {
        return array(
            'no spaces' => array('OOO "ЕвроАрт"', 'OOO "ЕвроАрт"'),
            'space before' => array('OOO "ЕвроАрт"', ' OOO "ЕвроАрт"'),
            'space after' => array('OOO "ЕвроАрт"', 'OOO "ЕвроАрт" '),
            'space before and after' => array('OOO "ЕвроАрт"', ' OOO "ЕвроАрт" '),
            'many spaces' => array('OOO "ЕвроАрт"', '    OOO "ЕвроАрт"  '),
        );
    }
}
