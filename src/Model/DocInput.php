<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class DocInput
{
    public function __construct(
        public array $value,
        public string $action = Index::ACTION_MERGE_OR_UPLOAD
    ) {
    }

    public function toArray(): array
    {
        $this->value['@search.action'] = $this->action;

        return $this->value;
    }
}