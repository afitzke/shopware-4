<?php
/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

class Shopware_StoreApi_Core_Gateway_Auth extends Shopware_StoreApi_Core_Gateway_Gateway
{
    public function login($shopwareID, $password)
    {
        $json = array(
            'shopwareID' => $shopwareID,
            'password' => $password
        );

        return $this->get('auth/login', $json);
    }

    public function isTokenValid($shopwareID, $token)
    {
        $json = array(
            'shopwareID' => $shopwareID,
            'token' => $token
        );

        return $this->get('auth/check', $json);
    }
}
