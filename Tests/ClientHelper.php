<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\Adapter\Elasticsearch\Tests;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;

final class ClientHelper
{
    private static ?Client $client = null;

    public static function getClient(): Client
    {
        if (!self::$client instanceof \Elastic\Elasticsearch\Client) {
            self::$client = ClientBuilder::create()->setHosts([
                $_ENV['ELASTICSEARCH_HOST'] ?? '127.0.0.1:9200',
            ])->build();
        }

        return self::$client;
    }
}
