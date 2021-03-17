<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\QuoteIdMaskFactory;

class DataQuoteTest extends TestCase
{

    protected $model;

    protected $checkoutSession;

    protected $quoteIdMaskFactory;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->checkoutSession = $this->getMockBuilder(CheckoutSession::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteIdMaskFactory = $this->getMockBuilder(QuoteIdMaskFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            \Constantino\CatalogSearchShipping\Model\DataQuote::class,
            [
                'checkoutSession' => $this->checkoutSession,
                'quoteIdMaskFactory' => $this->quoteIdMaskFactory
            ]
        );
    }

    public function testGetHasQuoteEmpty()
    {
        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);

        $this->checkoutSession->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);

        $quote->expects($this->any())
            ->method('getItems')
            ->willReturn([]);

        $result = $this->model->getHasQuote();
        $this->assertFalse($result);
    }

    public function testGetHasQuoteNotEmpty()
    {
        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);

        $this->checkoutSession->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);

        $quote->expects($this->any())
            ->method('getItems')
            ->willReturn(['teste']);

        $result = $this->model->getHasQuote();
        $this->assertTrue($result);
    }

    public function testGetQuoteId()
    {
        $this->checkoutSession->expects($this->any())
            ->method('getQuoteId')
            ->willReturn(1);

        $result = $this->model->getQuoteId();
        $this->assertEquals(1, $result);
    }

    public function testGetQuoteMaskId()
    {
        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteMask = $this->createMock(\Magento\Quote\Model\QuoteIdMask::class);
        $quoteIdMask = $this->getMockBuilder(\Magento\Framework\Model\AbstractModel::class)
            ->setMethods(['getMaskedId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutSession->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);

        $quote->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->quoteIdMaskFactory->expects($this->any())
            ->method('create')
            ->willReturn($quoteMask);

        $quoteMask->expects($this->any())
            ->method('load')
            ->with(1, 'quote_id')
            ->willReturn($quoteIdMask);

        $quoteIdMask->expects($this->any())
            ->method('getMaskedId')
            ->willReturn('rewqreqwrewq');

        $result = $this->model->getQuoteMaskId();
        $this->assertEquals("rewqreqwrewq", $result);
    }

    public function testGetQuoteMaskIdFalse()
    {
        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteMask = $this->createMock(\Magento\Quote\Model\QuoteIdMask::class);
        $quoteIdMask = $this->createMock(\Magento\Framework\Model\AbstractModel::class);

        $this->checkoutSession->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);

        $quote->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->quoteIdMaskFactory->expects($this->any())
            ->method('create')
            ->willReturn($quoteMask);

        $quoteMask->expects($this->once())
            ->method('load')
            ->with(null, 'quote_id')
            ->willReturn($quoteIdMask);

        $result = $this->model->getQuoteMaskId();
        $this->assertFalse($result);
    }

}
