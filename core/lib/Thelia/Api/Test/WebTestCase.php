<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Api\Test;

use Propel\Runtime\Propel;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestAssertionsTrait;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Api\Test\Abstract\WebTestCase as BaseWebTestCase;
use Thelia\Config\DatabaseConfiguration;

/**
 * WebTestCase is the base class for functional tests.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class WebTestCase extends BaseWebTestCase
{
    use WebTestAssertionsTrait;

    protected static $client;

    protected static $con;

    protected static ?string $tokenAdmin = null;

    protected static ?string $tokenCustomer = null;

    protected function tearDown(): void
    {
        static::$con->rollBack();
        parent::tearDown();
        static::$client = null;
        static::$con = null;
    }

    protected function setUp(): void
    {
        parent::setUp();
        if (null === self::$client) {
            self::$client = static::createClient();
        }
        if (static::$con === null) {
            static::$con = Propel::getWriteConnection(DatabaseConfiguration::THELIA_CONNECTION_NAME);
        }
        static::$con->beginTransaction();
    }

    public function loginAdmin(AbstractBrowser $client): void
    {
        if (null !== self::$tokenAdmin) {
            $client->setServerParameter(key: 'HTTP_Authorization',value:  sprintf('Bearer %s',self::$tokenAdmin));
            $client->setServerParameter(key: 'HTTP_ACCEPT',value:  'application/ld+json');
            $client->setServerParameter(key: 'CONTENT_TYPE',value:  'application/ld+json');
            return;
        }
        $credentials = [
            'username' => 'admin',
            'password' => 'admin',
        ];

        $client->request(
            method: 'POST',
            uri: sprintf('%s%s', $_ENV['API_BASE_URL'], '/admin/login'),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($credentials, JSON_THROW_ON_ERROR),
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($response->getStatusCode() !== Response::HTTP_OK || !isset($data['token'])) {
            throw new \RuntimeException('Failed to log in as admin. Check your credentials and login endpoint.');
        }
        self::$tokenAdmin = $data['token'];
        $client->setServerParameter(key: 'HTTP_Authorization',value:  sprintf('Bearer %s', $data['token']));
        $client->setServerParameter(key: 'HTTP_ACCEPT',value:  'application/ld+json');
        $client->setServerParameter(key: 'CONTENT_TYPE',value:  'application/ld+json');
    }

    public function loginCustomer(AbstractBrowser $client): void
    {
        if (null !== self::$tokenCustomer) {
            $client->setServerParameter(key: 'HTTP_Authorization',value:  sprintf('Bearer %s',self::$tokenCustomer));
            $client->setServerParameter(key: 'HTTP_ACCEPT',value:  'application/ld+json');
            $client->setServerParameter(key: 'CONTENT_TYPE',value:  'application/ld+json');
            return;
        }
        $credentials = [
            'username' => 'test@thelia.net',
            'password' => 'thelia',
        ];

        $client->request(
            method: 'POST',
            uri: sprintf('%s%s', $_ENV['API_BASE_URL'], '/front/login'),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($credentials, JSON_THROW_ON_ERROR),
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($response->getStatusCode() !== Response::HTTP_OK || !isset($data['token'])) {
            throw new \RuntimeException('Failed to log in as admin. Check your credentials and login endpoint.');
        }
        self::$tokenCustomer = $data['token'];
        $client->setServerParameter(key: 'HTTP_Authorization',value:  sprintf('Bearer %s', $data['token']));
        $client->setServerParameter(key: 'HTTP_ACCEPT',value:  'application/ld+json');
        $client->setServerParameter(key: 'CONTENT_TYPE',value:  'application/ld+json');
    }

    public function login($client, string $uri): void
    {
        if (str_contains($uri, '/admin')) {
            $this->loginAdmin($client);
            return;
        }
        if(str_contains($uri, '/front')) {
            $this->loginCustomer($client);
            return;
        }
        throw new RuntimeException(sprintf('Cannot log for the route %s', $uri));
    }
}
