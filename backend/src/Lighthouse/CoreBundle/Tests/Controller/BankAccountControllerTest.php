<?php

namespace Lighthouse\CoreBundle\Tests\Controller;

use Lighthouse\CoreBundle\Test\WebTestCase;

class BankAccountControllerTest extends WebTestCase
{
    /**
     * @dataProvider organizationPostActionProvider
     * @param array $postData
     * @param $expectedCode
     * @param array $assertions
     */
    public function testOrganizationPostAction(array $postData, $expectedCode, array $assertions)
    {
        $organization = $this->factory()->organization()->getOrganization();

        $accessToken = $this->factory()->oauth()->authAsProjectUser();

        $postResponse = $this->clientJsonRequest(
            $accessToken,
            'POST',
            "/api/1/organizations/{$organization->id}/bankAccounts",
            $postData
        );

        $this->assertResponseCode($expectedCode);

        $this->performJsonAssertions($postResponse, $assertions);
    }

    /**
     * @return array
     */
    public function organizationPostActionProvider()
    {
        return array(
            'valid only account' => array(
                array(
                    'account' => '40807979401000271609'
                ),
                201,
                array(
                    'account' => '40807979401000271609',
                    'organization.name' => 'Организация',
                )
            ),
            'valid all fields' => array(
                array(
                    'bic' => '044030786',
                    'bankName' => 'ФИЛИАЛ "САНКТ-ПЕТЕРБУРГСКИЙ" ОАО "АЛЬФА-БАНК"',
                    'bankAddress' => '191123, САНКТ-ПЕТЕРБУРГ, УЛ.ФУРШТАТСКАЯ,40,ЛИТ.А',
                    'correspondentAccount' => '30101810600000000786',
                    'account' => '40807979401000271609',
                ),
                201,
                array(
                    'bic' => '044030786',
                    'bankName' => 'ФИЛИАЛ "САНКТ-ПЕТЕРБУРГСКИЙ" ОАО "АЛЬФА-БАНК"',
                    'bankAddress' => '191123, САНКТ-ПЕТЕРБУРГ, УЛ.ФУРШТАТСКАЯ,40,ЛИТ.А',
                    'correspondentAccount' => '30101810600000000786',
                    'account' => '40807979401000271609',
                    'organization.name' => 'Организация',
                )
            ),
            'invalid required fields' => array(
                array(
                    'bic' => '044030786',
                    'bankName' => 'ФИЛИАЛ "САНКТ-ПЕТЕРБУРГСКИЙ" ОАО "АЛЬФА-БАНК"',
                    'bankAddress' => '191123, САНКТ-ПЕТЕРБУРГ, УЛ.ФУРШТАТСКАЯ,40,ЛИТ.А',
                    'correspondentAccount' => '30101810600000000786',
                    'account' => '',
                ),
                400,
                array(
                    'children.account.errors.0' => 'Заполните это поле'
                )
            ),
            'invalid max length' => array(
                array(
                    'bankName' => str_repeat('b', 301),
                    'bankAddress' => str_repeat('b', 301),
                    'correspondentAccount' => str_repeat('1', 31),
                    'account' => str_repeat('0', 101),
                ),
                400,
                array(
                    'children.bankName.errors.0' => 'Не более 300 символов',
                    'children.bankAddress.errors.0' => 'Не более 300 символов',
                    'children.correspondentAccount.errors.0' => 'Не более 30 символов',
                    'children.account.errors.0' => 'Не более 100 символов',
                )
            ),
            'invalid bic max length' => array(
                array(
                    'bic' => '0440307861',
                ),
                400,
                array(
                    'children.bic.errors.0' => 'БИК должен состоять из 9 цифр',
                )
            ),
            'invalid bic min length' => array(
                array(
                    'bic' => '04403078',
                ),
                400,
                array(
                    'children.bic.errors.0' => 'БИК должен состоять из 9 цифр',
                )
            ),
            'invalid bic not digits' => array(
                array(
                    'bic' => '044 307B',
                ),
                400,
                array(
                    'children.bic.errors.0' => 'БИК должен состоять из 9 цифр',
                )
            ),
        );
    }
}