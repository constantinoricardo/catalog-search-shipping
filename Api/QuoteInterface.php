<?php

declare(strict_types=1);

namespace Constantino\CatalogSearchShipping\Api;

interface QuoteInterface
{

    /**
     * @api
     * @return string
     */
    public function create(int $productId): string;

    /**
     * @api
     * @return void
     */
    public function delete(): void;
}
