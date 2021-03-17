<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\QuoteIdMaskFactory;

class DataQuote
{

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * DataQuote constructor.
     * @param CheckoutSession $checkoutSession
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getHasQuote()
    {
        $items = $this->checkoutSession->getQuote()->getItems();
        return (!empty($items)) ? true : false;
    }

    /**
     * @return int
     */
    public function getQuoteId()
    {
        return $this->checkoutSession->getQuoteId();
    }

    /**
     * @return bool|int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuoteMaskId()
    {
        $quoteId = $this->checkoutSession->getQuote()->getId();
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
        return (!empty($quoteId)) ? $quoteIdMask->getMaskedId() : false;
    }

}
