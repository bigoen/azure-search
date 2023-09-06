<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class Suggester
{
    public const DEFAULT_SEARCH_MODE = 'analyzingInfixMatching';

    public function __construct(
        public string $name,
        public array $sourceFields,
        public string $searchMode = self::DEFAULT_SEARCH_MODE
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'sourceFields' => $this->sourceFields,
            'searchMode' => $this->searchMode,
        ];
    }
}