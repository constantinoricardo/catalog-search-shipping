<?php


declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Api;

use Magento\Quote\Api\Data\AddressInterface;

interface SearchShippingsInterface
{

    /**
     * @api
     * @param AddressInterface $address
     * @param int $productId
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods
     */
    public function execute(AddressInterface $address, int $productId);
}
