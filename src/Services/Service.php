<?php

declare(strict_types=1);

namespace Bigoen\AzureSearch\Services;

use Bigoen\AzureSearch\Model\Doc;
use Bigoen\AzureSearch\Model\DocInput;
use Bigoen\AzureSearch\Model\Error;
use Bigoen\AzureSearch\Model\Index;
use Bigoen\AzureSearch\Model\IndexSearch;
use Bigoen\AzureSearch\Model\IndexStat;
use Bigoen\AzureSearch\Model\IndexSuggest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Şafak Saylam <safak@bigoen.com>
 */
final class Service
{
    private array $defaultOptions = [];
    private int $batchLimit = 1000;

    public function __construct(
        string $url,
        string $apiKey,
        string $version,
        private readonly HttpClientInterface $client
    ) {
        if ('/' !== @$url[-1]) {
            $url = "$url/";
        }
        $this->defaultOptions = [
            'base_uri' => $url,
            'headers' => [
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'api-version' => $version,
            ],
            'timeout' => 10,
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function createIndex(Index $index): Index|Error
    {
        $data = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_POST, 'indexes', ['json' => $index->toArray()])
            ->toArray(false);

        return $this->error($data) ?? Index::fromArray($data);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function updateIndex(Index $index): true|Error
    {
        $response = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_PUT, "indexes/{$index->getName()}", ['json' => $index->toArray()]);

        return Response::HTTP_NO_CONTENT === $response->getStatusCode() ? true : $this->error($response->toArray(false));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function deleteIndex(string $name): bool
    {
        $statusCode = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_DELETE, "indexes/$name")
            ->getStatusCode();

        return Response::HTTP_NO_CONTENT === $statusCode;
    }

    /**
     * @return array<int, Index>
     *
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getIndexes(): array|Error
    {
        $data = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_GET, 'indexes')
            ->toArray(false);

        return $this->error($data) ?? array_map(fn (array $value) => Index::fromArray($value), $data['value']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getIndex(string $name): Index|Error
    {
        $data = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_GET, "indexes/$name")
            ->toArray(false);

        return $this->error($data) ?? Index::fromArray($data);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getIndexStats(string $name): IndexStat|Error
    {
        $data = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_GET, "indexes/$name/stats")
            ->toArray(false);

        return $this->error($data) ?? IndexStat::fromArray($data);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getDocCount(string $name): int|Error
    {
        $response = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_GET, "indexes/$name/docs/".'$count');

        return Response::HTTP_OK === $response->getStatusCode()
            ? (int) filter_var($response->getContent(false), FILTER_SANITIZE_NUMBER_INT)
            : $this->error($response->toArray(false));
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function addDocToIndex(string $name, DocInput $input): array|Error
    {
        $data = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_POST, "indexes/$name/docs/index", ['json' => ['value' => [$input->toArray()]]])
            ->toArray(false);

        return $this->error($data) ?? array_map(fn (array $value) => Doc::fromArray($value), $data['value']);
    }

    /**
     * @param  array<int, DocInput>  $inputs
     *
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function addDocsToIndex(string $name, array $inputs): array|Error
    {
        $data = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_POST, "indexes/$name/docs/index", ['json' => ['value' => array_map(fn (DocInput $input) => $input->toArray(), $inputs)]])
            ->toArray(false);

        return $this->error($data) ?? array_map(fn (array $value) => Doc::fromArray($value), $data['value']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getDoc(string $name, string $key): array|Error
    {
        $data = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_GET, "indexes/$name/docs/$key")
            ->toArray(false);
        $error = $this->error($data);
        if ($error instanceof Error) {
            return $error;
        }
        unset($data['@odata.context']);

        return $data;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function search(string $name, string $search, array $arguments = []): IndexSearch|Error
    {
        $data = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_POST, "indexes/$name/docs/search", ['json' => array_merge([
                'search' => $search,
            ], $arguments)])
            ->toArray(false);

        return $this->error($data) ?? IndexSearch::fromArray($data);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function suggestions(string $name, string $search, string $suggesterName, array $arguments = []): array|Error
    {
        $data = $this->client
            ->withOptions($this->defaultOptions)
            ->request(Request::METHOD_POST, "indexes/$name/docs/suggest", ['json' => array_merge([
                'search' => $search,
                'suggesterName' => $suggesterName,
            ], $arguments)])
            ->toArray(false);

        return $this->error($data) ?? array_map(fn (array $value) => IndexSuggest::fromArray($value), $data['value']);
    }

    private function error(array $data): ?Error
    {
        return isset($data['error']) ? new Error($data['error']['code'], $data['error']['message'], $data['error']['details'] ?? []) : null;
    }
}