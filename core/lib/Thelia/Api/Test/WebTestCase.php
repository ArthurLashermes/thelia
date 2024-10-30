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

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestAssertionsTrait;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\Response;

/**
 * WebTestCase is the base class for functional tests.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class WebTestCase extends KernelTestCase
{
    use WebTestAssertionsTrait;

    protected function tearDown(): void
    {
        parent::tearDown();
        self::getClient(null);
    }

    /**
     * Creates a KernelBrowser.
     *
     * @param array $options An array of options to pass to the createKernel method
     * @param array $server  An array of server parameters
     */
    protected static function createClient(array $options = [], array $server = []): AbstractBrowser
    {
        if (static::$booted) {
            throw new \LogicException(sprintf('Booting the kernel before calling "%s()" is not supported, the kernel should only be booted once.', __METHOD__));
        }

        $kernel = static::bootKernel($options);

        try {
            $client = $kernel->getContainer()->get('test.client');
        } catch (ServiceNotFoundException) {
            if (class_exists(KernelBrowser::class)) {
                throw new \LogicException('You cannot create the client used in functional tests if the "framework.test" config is not set to true.');
            }
            throw new \LogicException('You cannot create the client used in functional tests if the BrowserKit component is not available. Try running "composer require symfony/browser-kit".');
        }

        $client->setServerParameters($server);

        return self::getClient($client);
    }

    public function loginAdmin(AbstractBrowser $client): void
    {
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

        $client->setServerParameter(key: 'HTTP_Authorization',value:  sprintf('Bearer %s', $data['token']));
        $client->setServerParameter(key: 'HTTP_ACCEPT',value:  'application/ld+json');
        $client->setServerParameter(key: 'CONTENT_TYPE',value:  'application/ld+json');
    }

    public function loginCustomer(AbstractBrowser $client): void
    {
        $credentials = [
            'username' => 'thelia',
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

        $client->setServerParameter(key: 'HTTP_Authorization',value:  sprintf('Bearer %s', $data['token']));
        $client->setServerParameter(key: 'HTTP_ACCEPT',value:  'application/ld+json');
        $client->setServerParameter(key: 'CONTENT_TYPE',value:  'application/ld+json');
    }
}
