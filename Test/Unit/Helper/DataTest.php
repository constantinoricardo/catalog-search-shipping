<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Config;

class DataTest extends TestCase
{

    protected $model;

    protected $scopeConfigInterface;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->scopeConfigInterface = $this->createMock(Config::class);

        $context = $this->createMock(\Magento\Framework\App\Helper\Context::class);

        $this->model = $objectManager->getObject(
            \Constantino\CatalogSearchShipping\Helper\Data::class,
            [
                'context' => $context,
                'scopeConfig' => $this->scopeConfigInterface
            ]
        );
    }

    public function testGetUriRequestEmpty()
    {
        $result = $this->model->getUriRequest();
        $this->assertEmpty($result);
    }

    public function testGetUriRequestNotEmpty()
    {
        $this->scopeConfigInterface->expects($this->any())
            ->method('getValue')
            ->with("search/constantino/api_correios", \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ->willReturn("terqrewq");

        $result = $this->model->getUriRequest();
        $this->assertNotEmpty($result);
    }

}
