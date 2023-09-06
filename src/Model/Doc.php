<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class Doc
{
    public function __construct(
        public string|int $key,
        public bool $status,
        public ?string $errorMessage,
        public ?int $statusCode
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['key'], $data['status'], $data['errorMessage'], $data['statusCode']);
    }
}