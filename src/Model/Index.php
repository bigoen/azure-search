<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Model;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class Index
{
    /**
     * An upload action is similar to an "upsert" where the document will be inserted if it is new and
     * updated/replaced if it exists. Note that all fields are replaced in the update case.
     */
    public const ACTION_UPLOAD = 'upload';

    /**
     * Merge updates an existing document with the specified fields. If the document doesn't exist, the merge
     * will fail. Any field you specify in a merge will replace the existing field in the document. This
     * includes fields of type Collection(Edm.String). For example, if the document contains a field "tags"
     * with value ["budget"] and you execute a merge with value ["economy", "pool"] for "tags", the final
     * value of the "tags" field will be ["economy", "pool"]. It will not be ["budget", "economy", "pool"].
     */
    public const ACTION_MERGE = 'merge';

    /**
     * This action behaves like merge if a document with the given key already exists in the index. If the
     * document does not exist, it behaves like upload with a new document.
     */
    public const ACTION_MERGE_OR_UPLOAD = 'mergeOrUpload';

    /**
     * Delete removes the specified document from the index. Note that any field you specify in a delete
     * operation, other than the key field, will be ignored. If you want to remove an individual field
     * from a document, use merge instead and simply set the field explicitly to null.
     */
    public const ACTION_DELETE = 'delete';

    public array $fields = [];
    public array $suggesters = [];
    private $corsOptions;

    public function __construct(private readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addField(Field $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    public function addSuggester(Suggester $suggester): self
    {
        $this->suggesters[] = $suggester;

        return $this;
    }

    public function setCorsOptions($corsOptions): self
    {
        $this->corsOptions = $corsOptions;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'fields' => array_map(fn (Field $field) => $field->toArray(), $this->fields),
            'suggesters' => array_map(fn (Suggester $suggester) => $suggester->toArray(), $this->suggesters),
            'corsOptions' => $this->corsOptions,
        ];
    }

    public static function fromArray(array $data): self
    {
        $object = new self($data['name']);
        foreach ($data['fields'] as $field) {
            $object->addField(new Field(
                $field['name'],
                $field['type'],
                $field['key'],
                $field['searchable'],
                $field['filterable'],
                $field['sortable'],
                $field['facetable'],
                $field['retrievable'],
                $field['analyzer']
            ));
        }
        foreach ($data['suggesters'] as $suggester) {
            $object->addSuggester(new Suggester(
                $suggester['name'],
                $suggester['sourceFields'],
                $suggester['searchMode']
            ));
        }
        $object->setCorsOptions($data['corsOptions']);

        return $object;
    }
}