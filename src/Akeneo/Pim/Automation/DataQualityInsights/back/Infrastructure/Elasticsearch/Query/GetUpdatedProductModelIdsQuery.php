<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2020 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\Automation\DataQualityInsights\Infrastructure\Elasticsearch\Query;

use Akeneo\Pim\Automation\DataQualityInsights\Domain\Query\ProductEvaluation\GetUpdatedProductIdsQueryInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Tool\Bundle\ElasticsearchBundle\Client;

class GetUpdatedProductModelIdsQuery implements GetUpdatedProductIdsQueryInterface
{
    /** @var Client */
    private $esClient;

    public function __construct(Client $esClient)
    {
        $this->esClient = $esClient;
    }

    public function since(\DateTimeImmutable $updatedSince, int $bulkSize): \Iterator
    {
        $query = [
            'bool' => [
                'must' => [
                    [
                        'term' => [
                            'document_type' => ProductModelInterface::class
                        ],
                    ],
                    [
                        'range' => [
                            'updated' => [
                                'gt' => $updatedSince->setTimezone(new \DateTimeZone('UTC'))->format('c')
                            ],
                        ]
                    ],
                ],
            ],
        ];

        $totalProductModels = $this->countUpdatedProductModels($query);

        $searchQuery = [
            '_source' => ['id'],
            'size' => $bulkSize,
            'sort' => ['_id' => 'asc'],
            'query' => $query
        ];

        $result = $this->esClient->search($searchQuery);
        $searchAfter = [];
        $returnedProductModels = 0;

        while (!empty($result['hits']['hits'])) {
            $productModelIds = [];
            $previousSearchAfter = $searchAfter;
            foreach ($result['hits']['hits'] as $product) {
                $productModelIds[] = $this->formatProductModelId($product);
                $searchAfter = $product['sort'] ?? $searchAfter;
            }

            yield $productModelIds;

            $returnedProductModels += count($productModelIds);
            $result = $returnedProductModels < $totalProductModels && $searchAfter !== $previousSearchAfter
                ? $this->searchAfter($searchQuery, $searchAfter)
                : [];
        }
    }

    private function searchAfter(array $query, array $searchAfter): array
    {
        if (!empty($searchAfter)) {
            $query['search_after'] = $searchAfter;
        }

        return $this->esClient->search($query);
    }

    private function formatProductModelId(array $productData): int
    {
        if (!isset($productData['_source']['id'])) {
            throw new \Exception('No id not found in source when searching updated product models');
        }

        return intval(str_replace('product_model_', '', $productData['_source']['id']));
    }

    private function countUpdatedProductModels(array $query): int
    {
        $count = $this->esClient->count([
            'query' => $query,
        ]);

        return $count['count'] ?? 0;
    }
}
