<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\SearchCustomers;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\SearchCustomersHandlerInterface;

/**
 * Handles command that searches for customers
 *
 * @internal
 */
final class SearchCustomersHandler implements SearchCustomersHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(SearchCustomers $query)
    {
        $queries = explode(' ', $query->getQuery());
        $queries = array_unique($queries);

        $customers = [];

        foreach ($queries as $searchQuery) {
            if (empty($searchQuery)) {
                continue;
            }

            $limit = 50;
            $customersResult = Customer::searchByName($searchQuery, $limit);
            if (!is_array($customersResult)) {
                continue;
            }

            foreach ($customersResult as $customerArray) {
                if ($customerArray['active']) {
                    $customerArray['fullname_and_email'] = sprintf(
                        '%s %s - %s',
                        $customerArray['firstname'],
                        $customerArray['lastname'],
                        $customerArray['email']
                    );
                    $customers[$customerArray['id_customer']] = $customerArray;
                }
            }
        }

        return $customers;
    }
}
