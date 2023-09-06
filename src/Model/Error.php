<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class Error
{
    public array $details = [];

    public function __construct(
        public readonly ?string $code,
        public readonly ?string $message,
        array $details = []
    ) {
        $this->details = array_map(fn (array $detail) => new Error($detail['code'], $detail['message']), $details);
    }
}