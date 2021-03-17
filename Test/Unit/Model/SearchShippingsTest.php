<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Test\Unit\Model;

use Constantino\CatalogSearchShipping\Model\DataQuote;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Api\GuestShipmentEstimationInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Constantino\CatalogSearchShipping\Api\QuoteInterface;
use Constantino\CatalogSearchShipping\Model\Service\CorreiosCep;

class SearchShippingsTest extends TestCase
{

    private $model;

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

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->customerSession = $this->getMockBuilder(CustomerSession::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->guestShipmentEstimationInterface = $this->getMockBuilder(GuestShipmentEstimationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->shipmentEstimationInterface = $this->getMockBuilder(ShipmentEstimationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataQuote = $this->getMockBuilder(DataQuote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteInterface = $this->getMockBuilder(QuoteInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->correiosCep = $this->getMockBuilder(CorreiosCep::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            \Constantino\CatalogSearchShipping\Model\SearchShippings::class,
            [
                'customerSession' => $this->customerSession,
                'guestShipmentEstimationInterface' => $this->guestShipmentEstimationInterface,
                'shipmentEstimationInterface' => $this->shipmentEstimationInterface,
                'dataQuote' => $this->dataQuote,
                'quoteInterface' => $this->quoteInterface,
                'correiosCep' => $this->correiosCep
            ]
        );
    }

    public function testExecuteQuoteFalse()
    {
        $addressInterface = $this->createMock(AddressInterface::class);

        $addressInterface->expects($this->any())
            ->method('getPostcode')
            ->willReturn('05846-050');

        $this->customerSession->expects($this->any())
            ->method('getCustomerId')
            ->willReturn(10);

        $this->correiosCep->expects($this->any())
            ->method('getCity')
            ->with('05846-050')
            ->willReturn("cidade");

        $addressInterface->expects($this->any())
            ->method('setCity')
            ->with("cidade")
            ->willReturnSelf();

        $addressInterface->expects($this->any())
            ->method('setCountryId')
            ->with("BR")
            ->willReturnSelf();

        $this->dataQuote->expects($this->any())
            ->method('getHasQuote')
            ->willReturn(false);

        $this->quoteInterface->expects($this->any())
            ->method('create')
            ->with(1)
            ->willReturn('teste');

        $this->dataQuote->expects($this->any())
            ->method('getQuoteId')
            ->willReturn(1);

        $this->shipmentEstimationInterface->expects($this->any())
            ->method('estimateByExtendedAddress')
            ->with(1, $addressInterface)
            ->willReturn(['teste', 'outroteste']);

        $this->quoteInterface->expects($this->any())
            ->method('delete');

        $result = $this->model->execute($addressInterface, 1);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function testCityEmpty()
    {
        $addressInterface = $this->createMock(AddressInterface::class);

        $addressInterface->expects($this->any())
            ->method('getPostcode')
            ->willReturn('05846-050');

        $this->customerSession->expects($this->any())
            ->method('getCustomerId')
            ->willReturn(10);

        $this->correiosCep->expects($this->any())
            ->method('getCity')
            ->with('05846-050')
            ->willReturn("");

        $result = $this->model->execute($addressInterface, 1);
        $this->assertEmpty($result);
    }

    public function testExecuteCustomerNotNull()
    {
        $addressInterface = $this->createMock(AddressInterface::class);

        $addressInterface->expects($this->any())
            ->method('getPostcode')
            ->willReturn('05846-050');

        $this->customerSession->expects($this->any())
            ->method('getCustomerId')
            ->willReturn(10);

        $this->correiosCep->expects($this->any())
            ->method('getCity')
            ->with('05846-050')
            ->willReturn("cidade");

        $addressInterface->expects($this->any())
            ->method('setCity')
            ->with("cidade")
            ->willReturnSelf();

        $addressInterface->expects($this->any())
            ->method('setCountryId')
            ->with("BR")
            ->willReturnSelf();

        $this->dataQuote->expects($this->any())
            ->method('getHasQuote')
            ->willReturn(true);

        $this->dataQuote->expects($this->any())
            ->method('getQuoteId')
            ->willReturn(1);

        $this->shipmentEstimationInterface->expects($this->any())
            ->method('estimateByExtendedAddress')
            ->with(1, $addressInterface)
            ->willReturn(['teste', 'outroteste']);

        $result = $this->model->execute($addressInterface, 1);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function testExecuteCustomerNull()
    {
        $addressInterface = $this->createMock(AddressInterface::class);

        $addressInterface->expects($this->any())
            ->method('getPostcode')
            ->willReturn('05846-050');

        $this->dataQuote->expects($this->any())
            ->method('getHasQuote')
            ->willReturn(true);

        $this->correiosCep->expects($this->any())
            ->method('getCity')
            ->with('05846-050')
            ->willReturn("cidade");

        $addressInterface->expects($this->any())
            ->method('setCity')
            ->with("cidade")
            ->willReturnSelf();

        $addressInterface->expects($this->any())
            ->method('setCountryId')
            ->with("BR")
            ->willReturnSelf();

        $this->dataQuote->expects($this->any())
            ->method('getQuoteMaskId')
            ->willReturn('rwq5325432');

        $this->guestShipmentEstimationInterface->expects($this->any())
            ->method('estimateByExtendedAddress')
            ->with('rwq5325432', $addressInterface)
            ->willReturn(['teste', 'outroteste']);

        $result = $this->model->execute($addressInterface, 1);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

}
