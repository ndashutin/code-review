<?php


namespace console\controllers;

use common\context\application\entity\ProductImportVersion\ProductImportVersionInterface;
use common\context\application\entity\ProductImportVersion\ProductImportVersionTypeEnum;
use common\context\application\repository\ProductImportVersion\ProductImportVersionRepositoryInterface;
use common\context\application\services\processor\product\ProductProcessorInterface;
use common\infrastructure\log\ApplicationLoggerInterface;
use console\components\import\DisComponentInterface;
use console\controllers\log\ConsoleStdoutLogTrait;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\Json;

/**
 * Class DisImportController
 * @package console\controllers
 */
class DisImportController extends Controller
{
    use ConsoleStdoutLogTrait;

    private ProductProcessorInterface $productProcessor;

    /**
     * @inheritDoc
     */
    public function __construct(
        string $id,
        Module $module,
        private ApplicationLoggerInterface $logger,
        private DisComponentInterface $dis,
        private ProductImportVersionInterface $importVersion,
        private ProductImportVersionRepositoryInterface $importVersionRepository,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }


    /**
     * Method actionProducts
     * @param ProductProcessorInterface $productProcessor
     * @throws GuzzleException
     */
    public function actionProducts(ProductProcessorInterface $productProcessor): void
    {
        $this->info('Start sync products');
        //get last import version
        $lastImport = $this->importVersionRepository->findLastImport(ProductImportVersionTypeEnum::products());

        //save new import version
        $this->importVersionRepository->saveVersion($this->importVersion::forProducts());

        $currentPage = 0;
        do {
            $currentPage++;

            $req = $this->dis->makeRequest('/products', [
                'page' => $currentPage,
                'withChangesFromDate' => $lastImport ? strtotime($lastImport->getDate()) : null,
                'expand' => 'productAttributes'
            ]);

            $items = Json::decode($req->getBody()->getContents(), true);
            foreach ($items as $item) {
                try {
                    $productProcessor->process($item);
                } catch (Exception $e) {
                    $this->logger->error(
                        'Failed to process product!',
                        [
                            'external_id' => $item['external_id'],
                            'error' => $e->getMessage()
                        ]
                    );
                }
            }

            $pageCount = $req->getHeader('x-pagination-page-count')[0] ?? null;
            $this->info("Complete page: $currentPage of $pageCount");
        } while ($req && $pageCount && $pageCount > $currentPage);

        $this->info('Finish sync products');
    }
}