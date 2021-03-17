<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Test\Unit\ViewModel;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Config;

class EnableSearchTest extends TestCase
{

    protected $model;

    protected $config;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->config = $this->createMock(Config::class);

        $this->model = $objectManager->getObject(
            \Constantino\CatalogSearchShipping\ViewModel\EnableSearch::class,
            [
                'scopeConfig' => $this->config
            ]
        );
    }

    public function testIsEnabled()
    {
        $this->config->expects($this->any())
            ->method('getValue')
            ->with("search/constantino/enable_search", \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ->willReturn(1);

        $result = $this->model->isEnabled();
        $this->assertTrue($result);
    }


}
