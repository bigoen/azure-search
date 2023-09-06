<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class IndexStat
{
    public function __construct(
        public int $documentCount,
        public int $storageSize
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['documentCount'], $data['storageSize']);
    }
}