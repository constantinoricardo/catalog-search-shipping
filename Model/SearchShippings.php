<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Model;

use Constantino\CatalogSearchShipping\Api\QuoteInterface;
use Constantino\CatalogSearchShipping\Model\Service\CorreiosCep;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\GuestShipmentEstimationInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;

class SearchShippings implements \Constantino\CatalogSearchShipping\Api\SearchShippingsInterface
{

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var GuestShipmentEstimationInterface
     */
    private $guestShipmentEstimationInterface;

    /**
     * @var ShipmentEstimationInterface
     */
    private $shipmentEstimationInterface;

    /**
     * @var \Constantino\CatalogSearchShipping\Model\DataQuote
     */
    private $dataQuote;

    /**
     * @var QuoteInterface
     */
    private $quoteInterface;

    private $correiosCep;

    /**
     * SearchShippings constructor.
     * @param CustomerSession $customerSession
     * @param GuestShipmentEstimationInterface $guestShipmentEstimationInterface
     * @param ShipmentEstimationInterface $shipmentEstimationInterface
     * @param \Constantino\CatalogSearchShipping\Model\DataQuote $dataQuote
     * @param QuoteInterface $quoteInterface
     */
    public function __construct(
        CustomerSession $customerSession,
        GuestShipmentEstimationInterface $guestShipmentEstimationInterface,
        ShipmentEstimationInterface $shipmentEstimationInterface,
        DataQuote $dataQuote,
        QuoteInterface $quoteInterface,
        CorreiosCep $correiosCep
    ) {
        $this->customerSession = $customerSession;
        $this->guestShipmentEstimationInterface = $guestShipmentEstimationInterface;
        $this->shipmentEstimationInterface = $shipmentEstimationInterface;
        $this->dataQuote = $dataQuote;
        $this->quoteInterface = $quoteInterface;
        $this->correiosCep = $correiosCep;
    }

    /**
     * @param AddressInterface $address
     * @param int $productId
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(AddressInterface $address, int $productId)
    {
        $customerId = $this->customerSession->getCustomerId();

        $city = $this->correiosCep->getCity($address->getPostcode());

        if (empty($city)) {
            return [];
        }

        $address->setCity($city);
        $address->setCountryId('BR');

        if (empty($customerId)) {
            if ($this->dataQuote->getHasQuote()) {
                return $this->searchShippingHasQuote($address);
            }
        } else {
            if ($this->dataQuote->getHasQuote()) {
                return $this->searchShipping($address);
            }
        }

        if (!$this->dataQuote->getHasQuote()) {
            return $this->createQuote($address, $productId);
        }
    }

    /**
     * @param AddressInterface $address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function searchShippingHasQuote(AddressInterface $address)
    {
        $quoteId = $this->dataQuote->getQuoteMaskId();
        return $this->guestShipmentEstimationInterface->estimateByExtendedAddress($quoteId, $address);
    }

    /**
     * @param AddressInterface $address
     * @param int $productId
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    private function createQuote(AddressInterface $address, int $productId)
    {
        $this->quoteInterface->create($productId);
        $data = $this->searchShipping($address);

        if (!empty($data)) {
            $this->quoteInterface->delete();
        }

        return $data;
    }

    /**
     * @param AddressInterface $address
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    private function searchShipping(AddressInterface $address)
    {
        $quoteId = $this->dataQuote->getQuoteId();
        return $this->shipmentEstimationInterface->estimateByExtendedAddress($quoteId, $address);
    }
}
