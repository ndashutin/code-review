<?php

namespace common\context\application\entity\ProductImportVersion;

/**
 * Interface ProductImportVersionInterface
 * @package common\context\application\entity\ProductImportVersion
 */
interface ProductImportVersionInterface
{
    /**
     * @return int
     */
    public function getId(): int;
/**
     * @return string
     */
    public function getDate(): string;

    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return bool
     */
    public function forPricesUpdate(): bool;

    /**
     * @return bool
     */
    public function forBalancesUpdate(): bool;
}