<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Model\Service;

use Constantino\CatalogSearchShipping\Helper\Data as HelperData;

class CorreiosCep
{

    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * CorreiosCep constructor.
     * @param HelperData $helperData
     */
    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param string $postcode
     * @return string
     */
    public function getCity(string $postcode): string
    {
        $url = $this->helperData->getUriRequest() . $postcode . "/json";
        $elements = file_get_contents($url);

        $result = json_decode($elements, true);
        return !empty($result['localidade']) ? $result['localidade'] : "";
    }
}
