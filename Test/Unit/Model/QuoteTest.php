<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote as Item;

class QuoteTest extends TestCase
{

    private $model;

    /**
     * @var CartManagementInterface
     */
    private $cartManagementInterface;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Item
     */
    private $item;

    private $quote;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->cartManagementInterface = $this->getMockBuilder(CartManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->formKey = $this->getMockBuilder(FormKey::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteIdMaskFactory = $this->getMockBuilder(QuoteIdMaskFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->checkoutSession = $this->getMockBuilder(CheckoutSession::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->item = $this->getMockBuilder(Item::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quote = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getQuote', 'getId', 'delete', 'load'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            \Constantino\CatalogSearchShipping\Model\Quote::class,
            [
                'cartManagementInterface' => $this->cartManagementInterface,
                'formKey' => $this->formKey,
                'cart' => $this->cart,
                'product' => $this->product,
                'quoteIdMaskFactory' => $this->quoteIdMaskFactory,
                'checkoutSession' => $this->checkoutSession,
                'item' => $this->item
            ]
        );
    }

    public function testDelete()
    {
        $productLoad = $this->createMock(\Magento\Framework\Model\AbstractModel::class);

        $this->checkoutSession->expects($this->any())
            ->method('getQuote')
            ->willReturn($this->quote);

        $this->quote->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->item->expects($this->any())
            ->method('load')
            ->with(1)
            ->willReturn($productLoad);

        $this->item->expects($this->any())
            ->method('delete');

        $this->model->delete();

    }

    public function testCreate()
    {
        $quoteMask = $this->createMock(\Magento\Quote\Model\QuoteIdMask::class);
        $quoteIdMask = $this->getMockBuilder(\Magento\Framework\Model\AbstractModel::class)
            ->setMethods(['getMaskedId'])
            ->disableOriginalConstructor()
            ->getMock();

        $productLoad = $this->createMock(\Magento\Framework\Model\AbstractModel::class);

        $this->product->expects($this->any())
            ->method('load')
            ->willReturn($productLoad);

        $this->cart->expects($this->any())
            ->method('save')
            ->willReturn($this->quote);

        $this->quote->expects($this->any())
            ->method('getQuote')
            ->willReturnSelf();

        $this->quote->expects($this->any())
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

        $result = $this->model->create(1);
        $this->assertEquals('rewqreqwrewq', $result);
    }
}
