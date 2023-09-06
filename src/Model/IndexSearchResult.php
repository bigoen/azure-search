<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class IndexSearchResult
{
    public function __construct(
        public float $searchScore,
        public array $value
    ) {
    }

    public static function fromArray(array $data): self
    {
        $searchScore = $data['@search.score'];
        unset($data['@search.score']);

        return new self($searchScore, $data);
    }
}