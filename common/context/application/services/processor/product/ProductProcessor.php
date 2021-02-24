<?php

namespace common\context\application\services\processor\product;

use common\context\application\factory\product\ProductFactoryInterface;
use common\context\application\repository\Product\ProductRepositoryInterface;
use common\context\application\services\processor\ProcessorInterface;
use common\context\application\services\processor\productAttribute\ProductAttributeProcessorInterface;
use common\infrastructure\log\ApplicationLoggerInterface;
use common\persistence\Exception\PersistenceLayerException;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class ProductProcessor implements ProductProcessorInterface, ProcessorInterface
{
    private ProductRepositoryInterface $repository;
    private ProductFactoryInterface $factory;
    private ApplicationLoggerInterface $logger;
    private ProductAttributeProcessorInterface $coprocessor;

    public function __construct(
        ProductRepositoryInterface $repository,
        ProductFactoryInterface $factory,
        ApplicationLoggerInterface $logger,
        ProductAttributeProcessorInterface $coprocessor
    )
    {
        $this->repository = $repository;
        $this->logger = $logger;
        $this->factory = $factory;
        $this->coprocessor = $coprocessor;
    }

    /**
     * @param array $data
     * @throws Exception
     * @throws \JsonException
     */
    public function process(Array $data): void
    {
        if (!isset($data['productAttributes'])) {
            throw new Exception("Can't process product without productAttributes"
                . " Data: " . json_encode($data, JSON_THROW_ON_ERROR, 512));
        }

        $attributes = ArrayHelper::map($data['productAttributes'], 'id', fn($item) => $item, 'attribute_id');

        $modelNumber = reset($attributes['modelNumber'])['value'] ?? null;

        if (!$product = $this->repository->getVendoredByExternalId($data['vendor_brand_name'], $data['external_id'])) {
            $product = $this->factory->create(
                $data['external_id'],
                $data['vendor_brand_name']
            );
        }

        try {
            $product
                ->setVendorCode($data['vendor_code'])
                ->setModelName($modelNumber);
            $this->repository->saveProduct($product);

            foreach (['name', 'vendor_gender_title', 'vendor_product_type_title', 'vendor_parent_product_type_title'] as $attributeName) {
                $data['productAttributes'][] = [
                    'attribute_id' => $attributeName,
                    'value' => $attributeName,
                    'category' => 'main', 'status' => 'active', 'type' => 'string'
                ];
            }

            foreach ($data['productAttributes'] as $attribute) {
                $attribute['product_id'] = $product->getId();
                $this->coprocessor->process($attribute);
            }
        } catch (PersistenceLayerException $e) {
            $this->logger->error(
                'Failed to process product!',
                [
                    'external_id' => $data['external_id'],
                    'vendor_brand_name' => $data['vendor_brand_name'],
                    'error' => $e->getMessage()
                ]
            );
        }
    }
}