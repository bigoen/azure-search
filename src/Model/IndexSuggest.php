<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class IndexSuggest
{
    public function __construct(
        public string $searchText,
        public array $value
    ) {
    }

    public static function fromArray(array $data): self
    {
        $searchText = $data['@search.text'];
        unset($data['@search.text']);

        return new self($searchText, $data);
    }
}