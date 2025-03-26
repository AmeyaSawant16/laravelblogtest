<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $hosts = config('elasticsearch.hosts');
        $username = config('elasticsearch.auth.username');
        $password = config('elasticsearch.auth.password');

        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->setBasicAuthentication($username, $password)
            ->setSSLVerification(false)
            ->build();
    }

    public function createPostIndex()
    {
        if (!$this->client->indices()->exists(['index' => 'posts'])) {   
            $params = [
                'index' => 'posts',
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 1
                    ],
                    'mappings' => [
                        'properties' => [
                            'id'                  => ['type' => 'integer'],
                            'title'               => ['type' => 'text'],
                            'content'             => ['type' => 'text'],
                            'tags'                => ['type' => 'text'],
                            'image'               => ['type' => 'text'],
                            'excerpt'             => ['type' => 'text'],
                            'meta_title'          => ['type' => 'text'],
                            'meta_description'    => ['type' => 'text'],
                        ]
                    ]
                ]
            ];

            $response = $this->client->indices()->create($params);
        }
    }

    public function getClient()
    {
        return $this->client;
    }

    public function indexDocument($index, $id, $data)
    {
        return $this->client->index([
            'index' => $index,
            'id' => $id,
            'body' => $data
        ]);
    }

    public function getDocument($index, $id)
    {
        try {
            return $this->client->get([
                'index' => $index,
                'id' => $id
            ])->asArray();
        } catch (\Exception $e) {
            return ['error' => 'Document not found', 'message' => $e->getMessage()];
        }
    }

    public function search($index, $field, $value)
    {
        return $this->client->search([
            'index' => $index,
            'body' => [
                'query' => [
                    'match' => [
                        $field => $value 
                    ]
                ]
            ]
        ])->asArray();
    }

    public function searchIndex($index, $query)
    {
        try {
            $response = $this->client->search([
                'index' => $index,
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'query' => $query,
                            'fields' => ['title', 'content', 'tags', 'excerpt', 'meta_title', 'meta_description']
                        ]
                    ]
                ]
            ]);

            return $response->asArray();
        } catch (\Exception $e) {
            return ['error' => 'Search failed', 'message' => $e->getMessage()];
        }
    }

    public function updateDocument($index, $id, $data)
    {
        try {
            return $this->client->update([
                'index' => $index,
                'id' => $id,
                'body' => [
                    'doc' => $data
                ]
            ]);
        } catch (\Exception $e) {
            return ['error' => 'Update failed', 'message' => $e->getMessage()];
        }
    }

    public function deleteDocument($index, $id)
    {
        try {
            return $this->client->delete([
                'index' => $index,
                'id' => $id
            ]);
        } catch (\Exception $e) {
            return ['error' => 'Delete failed', 'message' => $e->getMessage()];
        }
    }

    public function deleteAllDocuments($index)
    {
        try {
            return $this->client->deleteByQuery([
                'index' => $index,
                'body' => [
                    'query' => [
                        'match_all' => (object)[] 
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return ['error' => 'Delete all failed', 'message' => $e->getMessage()];
        }
    }

    public function deleteIndex($index)
    {
        try {
            return $this->client->indices()->delete([
                'index' => $index
            ]);
        } catch (\Exception $e) {
            return ['error' => 'Delete index failed', 'message' => $e->getMessage()];
        }
    }

}
