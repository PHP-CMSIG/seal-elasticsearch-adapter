<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch\Tests;

use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;
use Schranz\Search\SEAL\Testing\TestingHelper;

class ElasticsearchSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$schemaManager = new ElasticsearchSchemaManager(self::$client);
    }

    public function testSimpleElasticsearchMapping(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_SIMPLE];
        $task = static::$schemaManager->createIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $mapping = self::$client->indices()->getMapping([
            'index' => $index->name,
        ])->asArray();

        $task = static::$schemaManager->dropIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $this->assertTrue(isset($mapping[$index->name]['mappings']['properties']));

        $this->assertSame([
            'id' => [
                'type' => 'keyword',
                'index' => false,
            ],
            'title' => [
                'type' => 'text',
            ],
        ], $mapping[$index->name]['mappings']['properties']);
    }

    public function testComplexElasticsearchMapping(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_COMPLEX];
        $task = static::$schemaManager->createIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $mapping = self::$client->indices()->getMapping([
            'index' => $index->name,
        ])->asArray();

        $task = static::$schemaManager->dropIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $this->assertTrue(isset($mapping[$index->name]['mappings']['properties']));

        $this->assertSame([
            'article' => [
                'type' => 'text',
            ],
            'blocks' => [
                'properties' => [
                    'embed' => [
                        'properties' => [
                            'media' => [
                                'type' => 'text',
                                'index' => false,
                            ],
                            'title' => [
                                'type' => 'text',
                            ],
                        ],
                    ],
                    'text' => [
                        'properties' => [
                            'description' => [
                                'type' => 'text',
                            ],
                            'media' => [
                                'type' => 'integer',
                                'index' => false,
                                'doc_values' => false,
                            ],
                            'title' => [
                                'type' => 'text',
                            ],
                        ],
                    ],
                ],
            ],
            'categoryIds' => [
                'type' => 'integer',
                'index' => false,
            ],
            'comments' => [
                'properties' => [
                    'email' => [
                        'type' => 'text',
                        'index' => false,
                    ],
                    'text' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'commentsCount' => [
                'type' => 'integer',
                'index' => false,
            ],
            'created' => [
                'type' => 'date',
            ],
            'footer' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'header' => [
                'properties' => [
                    'image' => [
                        'properties' => [
                            'media' => [
                                'type' => 'integer',
                                'index' => false,
                                'doc_values' => false,
                            ],
                        ],
                    ],
                    'video' => [
                        'properties' => [
                            'media' => [
                                'type' => 'text',
                                'index' => false,
                            ],
                        ],
                    ],
                ],
            ],
            'rating' => [
                'type' => 'float',
                'index' => false,
            ],
            'tags' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
            'title' => [
                'type' => 'text',
            ],
            'uuid' => [
                'type' => 'keyword',
                'index' => false,
            ],
        ], $mapping[$index->name]['mappings']['properties']);
    }
}
