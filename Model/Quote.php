<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Model;

use Magento\Quote\Api\CartManagementInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote as Item;

class Quote implements \Constantino\CatalogSearchShipping\Api\QuoteInterface
{

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

    /**
     * Quote constructor.
     * @param CartManagementInterface $cartManagementInterface
     * @param FormKey $formKey
     * @param Cart $cart
     * @param Product $product
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CheckoutSession $checkoutSession
     * @param Item $item
     */
    public function __construct(
        CartManagementInterface $cartManagementInterface,
        FormKey $formKey,
        Cart $cart,
        Product $product,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CheckoutSession $checkoutSession,
        Item $item
    ) {
        $this->cartManagementInterface = $cartManagementInterface;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->checkoutSession = $checkoutSession;
        $this->item = $item;
    }

    /**
     * @param int $productId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(int $productId): string
    {
        $params = array(
            'form_key' => $this->formKey->getFormKey(),
            'product' => $productId,
            'qty' => 1
        );

        $product = $this->product->load($productId);
        $this->cart->addProduct($product, $params);
        $quote = $this->cart->save();

        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quote->getQuote()->getId(), 'quote_id');
        return (!empty($quoteIdMask->getMaskedId())) ? $quoteIdMask->getMaskedId() : "";
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(): void
    {
        $quoteId = $this->checkoutSession->getQuote()->getId();

        $quoteItem = $this->item->load($quoteId);
        $quoteItem->delete();

    }
}
