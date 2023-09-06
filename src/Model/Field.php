<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class Field
{
    /**
     * Text that can optionally be tokenized for full-text search (word-breaking,
     * stemming, etc.)
     */
    public const TYPE_STRING = "Edm.String";

    /**
     * A list of strings that can optionally be tokenized for full-text search.
     * There is no theoretical upper limit on the number of items in a collection,
     * but the 16 MB upper limit on payload size applies to collection
     */
    public const TYPE_COLLECTION = "Collection(Edm.String)";

    /**
     * Contains true/false values.
     */
    public const TYPE_BOOLEAN = "Edm.Boolean";

    /**
     * 32-bit integer values.
     */
    public const TYPE_INT32 = "Edm.Int32";

    /**
     * 64-bit integer values.
     */
    public const TYPE_INT64 = "Edm.Int64";

    /**
     * Double-precision numeric data
     */
    public const TYPE_DOUBLE = "Edm.Double";

    /**
     * Date time values represented in the OData V4 format: yyyy-MM-ddTHH:mm:ss.fffZ
     * or yyyy-MM-ddTHH:mm:ss.fff[+|-]HH:mm. Precision of DateTime fields is limited
     * to milliseconds. If you upload datetime values with sub-millisecond precision,
     * the value returned will be rounded up to milliseconds (for example,
     * 2015-04-15T10:30:09.7552052Z will be returned as 2015-04-15T10:30:09.7550000Z).
     */
    public const TYPE_DATETIME_OFFSET = "Edm.DateTimeOffset";

    /**
     * A point representing a geographic location on the globe. For request and response
     * bodies the representation of values of this type follows the GeoJSON "Point"
     * type format. For URLs OData uses a literal form based on the WKT standard.
     * A point literal is constructed as geography'POINT(lon lat)'.
     */
    public const TYPE_GEOGRAPHY_POINT = "Edm.GeographyPoint";

    public function __construct(
        public string $name,
        public string $type,
        public bool $key = false,
        public bool $searchable = true,
        public bool $filterable = true,
        public bool $sortable = true,
        public bool $facetable = true,
        public bool $retrievable = true,
        public ?string $analyzer = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'searchable' => $this->searchable,
            'filterable' => $this->filterable,
            'sortable' => $this->sortable,
            'facetable' => $this->facetable,
            'key' => $this->key,
            'retrievable' => $this->retrievable,
            'analyzer' => $this->analyzer,
        ];
    }
}