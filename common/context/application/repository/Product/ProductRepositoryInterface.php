<?php

namespace common\context\application\repository\Product;

use common\context\application\entity\Product\ProductInterface;
use common\context\application\entity\TradeOffer\TradeOfferInterface;
use common\persistence\Exception\PersistenceLayerException;

/**
 * Interface ProductInterface
 * @package common\context\application\repository\Product
 */
interface ProductRepositoryInterface
{
    /**
     * Method getSkus
     * @param ProductInterface $product
     * @return array
     */
    public function getSkus(ProductInterface $product): array;

    /**
     * @param TradeOfferInterface $tradeOffer
     * @return ProductInterface
     */
    public function getForTradeOffer(TradeOfferInterface $tradeOffer): ProductInterface;

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     * @throws PersistenceLayerException
     */
    public function saveProduct(ProductInterface $product): ProductInterface;

    /**
     * @param string $vendorCode
     * @return ProductInterface[]
     */
    public function getByVendorCode(string $vendorCode): array;

    /**
     * @param string $externalId
     * @return ProductInterface[]
     */
    public function getByExternalId(string $externalId): array;

    /**
     * @param string $vendorId
     * @param string $externalId
     * @return ProductInterface|null
     */
    public function getVendoredByExternalId(string $vendorId, string $externalId): ?ProductInterface;

    /**
     * @param int $id
     * @return ProductInterface|null
     */
    public function getById(int $id): ?ProductInterface;

    /**
     * Method getAnyOneActiveProduct
     * @return ProductInterface|null
     */
    public function getAnyOneActiveProduct(): ?ProductInterface;

    /**
     * Method getAllProduct
     * @return array
     */
    public function getAllProduct(): array;
}
