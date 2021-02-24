<?php


namespace common\context\application\entity\ProductImportVersion;

/**
 * Class ProductImportVersionTypeEnum
 * @package common\context\application\entity\ProductImportVersion
 */
abstract class ProductImportVersionTypeEnum
{
    /**
     * @return string
     */
    public static function balances(): string
    {
        return 'balances';
    }

    /**
     * @return string
     */
    public static function prices(): string
    {
        return 'prices';
    }

    /**
     * @return string
     */
    public static function products(): string
    {
        return 'products';
    }
}