<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @return string
     */
    public function getUriRequest(): string
    {
        $result = $this->scopeConfig->getValue(
            "search/constantino/api_correios",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return (!empty($result)) ? $result : "";
    }
}
