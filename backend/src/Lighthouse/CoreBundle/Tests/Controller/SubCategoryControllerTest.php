<?php

namespace Lighthouse\CoreBundle\Tests\Controller;

use Lighthouse\CoreBundle\Test\Assert;
use Lighthouse\CoreBundle\Test\WebTestCase;

class SubCategoryControllerTest extends WebTestCase
{
    public function testPostSubCategoriesAction()
    {
        $this->clearMongoDb();

        $groupId = $this->createGroup('Алкоголь');
        $categoryId = $this->createCategory($groupId, 'Крепкий алкоголь');

        $subCategoryData = array(
            'name' => 'Водка',
            'category' => $categoryId,
        );

        $accessToken = $this->authAsRole('ROLE_COMMERCIAL_MANAGER');

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/subcategories',
            $subCategoryData
        );

        Assert::assertResponseCode(201, $this->client);

        Assert::assertJsonHasPath('id', $postResponse);
        Assert::assertJsonPathEquals('Водка', 'name', $postResponse);
        Assert::assertJsonPathEquals($categoryId, 'category.id', $postResponse);
        Assert::assertJsonPathEquals('Крепкий алкоголь', 'category.name', $postResponse);
        Assert::assertJsonPathEquals($groupId, 'category.group.id', $postResponse);
        Assert::assertJsonPathEquals('Алкоголь', 'category.group.name', $postResponse);
    }

    /**
     * @param int $expectedCode
     * @param array $data
     * @param array $assertions
     *
     * @dataProvider validationSubCategoryProvider
     */
    public function testPostSubCategoriesValidation($expectedCode, array $data, array $assertions = array())
    {
        $this->clearMongoDb();

        $groupId = $this->createGroup('Алкоголь');
        $categoryId = $this->createCategory($groupId, 'Крепкий алкоголь');

        $subCategoryData = $data + array(
            'name' => 'Водка',
            'category' => $categoryId,
        );

        $accessToken = $this->authAsRole('ROLE_COMMERCIAL_MANAGER');

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/subcategories',
            $subCategoryData
        );

        Assert::assertResponseCode($expectedCode, $this->client);

        $this->performJsonAssertions($postResponse, $assertions, true);
    }

    /**
     * @return array
     */
    public function validationSubCategoryProvider()
    {
        return array(
            'not valid empty name' => array(
                400,
                array('name' => ''),
                array(
                    'children.name.errors.0'
                    =>
                    'Заполните это поле'
                )
            ),
            'not valid long 101 name' => array(
                400,
                array('name' => str_repeat('z', 101)),
                array(
                    'children.name.errors.0'
                    =>
                    'Не более 100 символов'
                )
            ),
            'not valid category' => array(
                400,
                array('category' => '1234'),
                array(
                    'children.category.errors.*'
                    =>
                    'Такой категории не существует'
                )
            ),
            'valid long 100 name' => array(
                201,
                array('name' => str_repeat('z', 100)),
            ),
        );
    }

    public function testUniqueCategoryName()
    {
        $this->clearMongoDb();

        $groupId1 = $this->createGroup('Алкоголь');
        $groupId2 = $this->createGroup('Кисло-молочка');
        $categoryId1 = $this->createCategory($groupId1, 'Крепкий алкоголь');
        $categoryId2 = $this->createCategory($groupId2, 'Молоко');

        $subCategoryData = array(
            'name' => 'Водка',
            'category' => $categoryId1,
        );

        $accessToken = $this->authAsRole('ROLE_COMMERCIAL_MANAGER');
        // Create first category
        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/subcategories',
            $subCategoryData
        );

        Assert::assertResponseCode(201, $this->client);
        Assert::assertJsonHasPath('id', $postResponse);

        // Try to create second category with same name in group 1
        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/subcategories',
            $subCategoryData
        );
        Assert::assertResponseCode(400, $this->client);

        Assert::assertJsonPathContains(
            'Подкатегория с таким названием уже существует в этой категории',
            'children.name.errors',
            $postResponse
        );

        $subCategoryData2 = array('category' => $categoryId2) + $subCategoryData;

        // Create category with same name but in category 2
        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/subcategories',
            $subCategoryData2
        );

        Assert::assertResponseCode(201, $this->client);
        Assert::assertJsonHasPath('id', $postResponse);

        // Create second category with same name in category 2
        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/subcategories',
            $subCategoryData2
        );
        Assert::assertResponseCode(400, $this->client);

        Assert::assertJsonPathContains(
            'Подкатегория с таким названием уже существует в этой категории',
            'children.name.errors',
            $postResponse
        );
    }

    /**
     * @param int $expectedCode
     * @param array $data
     * @param array $assertions
     *
     * @dataProvider validationSubCategoryProvider
     */
    public function testPutSubCategoriesValidation($expectedCode, array $data, array $assertions = array())
    {
        $this->clearMongoDb();

        $groupId = $this->createGroup('Алкоголь');
        $categoryId = $this->createCategory($groupId, 'Крепкий алкоголь');

        $postData = array(
            'name' => 'Водка',
            'category' => $categoryId,
        );

        $accessToken = $this->authAsRole('ROLE_COMMERCIAL_MANAGER');

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            '/api/1/subcategories',
            $postData
        );

        Assert::assertResponseCode(201, $this->client);
        Assert::assertJsonHasPath('id', $postResponse);

        $subCategoryId = $postResponse['id'];

        $putData = $data + $postData;

        $putResponse = $this->clientJsonRequest(
            $accessToken,
            'PUT',
            '/api/1/subcategories/' . $subCategoryId,
            $putData
        );

        $expectedCode = (201 == $expectedCode) ? 200 : $expectedCode;

        Assert::assertResponseCode($expectedCode, $this->client);

        $this->performJsonAssertions($putResponse, $assertions, true);
    }
}
