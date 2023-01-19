<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Core\Security\UserProvider;

use Thelia\Core\Security\Token\TokenProvider;
use Thelia\Core\Security\User\UserInterface;

abstract class TokenUserProvider extends TokenProvider implements TokenUserProviderInterface
{
    abstract public function getUser(array $key): UserInterface;
}
