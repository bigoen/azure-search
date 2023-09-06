<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
class IndexSearch
{
    public array $results = [];
    public array $nextPageParameters = [];
    public ?string $nextLink = null;

    public static function fromArray(array $data): self
    {
        $object = new self();
        $object->results = array_map(fn (array $value) => IndexSearchResult::fromArray($value), $data['value']);
        $object->nextPageParameters = $data['@search.nextPageParameters'] ?? [];
        $object->nextLink = $data['@odata.nextLink'] ?? null;

        return $object;
    }
}