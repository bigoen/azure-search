Azure Cognitive Search Php SDK
==
**Install:**
```
composer require bigoen/azure-search
```

**Create Azure Service**
```php
use Bigoen\AzureSearch\Model\Error;
use Bigoen\AzureSearch\Model\Field;
use Bigoen\AzureSearch\Model\Index;
use Bigoen\AzureSearch\Model\Suggester;
use Bigoen\AzureSearch\Services\Service;
use Symfony\Component\HttpClient\HttpClient;

$azureService = new Service('endpoint', 'key', 'version', HttpClient::create());
```

**Create Index**
```php
$index = new Index('demo');
$index
    ->addField(new Field('id', Field::TYPE_STRING, true))
    ->addField(new Field('groupId', Field::TYPE_STRING))
    ->addField(new Field('title', Field::TYPE_STRING))
    ->addField(new Field('body', Field::TYPE_STRING))
    ->addSuggester(new Suggester('livesearch', ['title', 'body']));
$response = $azureService->createIndex($index);
if ($response instanceof Error) {
    // show error message.
}
```

**Update Index**
```php
$index = $azureService->getIndex('demo');
foreach ($index->fields as $field) {
    $field->sortable = false;
}
$response = $azureService->updateIndex($index);
if ($response instanceof Error) {
    // show error message.
}
```

**Delete Index**
```php
$isDeleted = $azureService->deleteIndex('demo');
if ($isDeleted) {
    // show success message.
}
```

**Add Docs/Doc to Index**
```php
$azureService->addDocToIndex($parameters['indexName'], new DocInput([
    'id' => '1',
    'groupId' => 'group1',
    'title' => 'Demo title',
    'body' => 'Demo body',
]));
// or multiple.
$azureService->addDocsToIndex($parameters['indexName'], [
    new DocInput([
        'id' => '1',
        'groupId' => 'group1',
        'title' => 'Demo title',
        'body' => 'Demo body',
    ], Index::ACTION_MERGE_OR_UPLOAD),
    new DocInput([
        'id' => '2',
        'groupId' => 'group1',
        'title' => 'Demo title 2',
        'body' => 'Demo body 2',
    ], Index::ACTION_DELETE),
]);
```