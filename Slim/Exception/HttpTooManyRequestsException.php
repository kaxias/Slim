<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/5.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Exception;

final class HttpTooManyRequestsException extends HttpSpecializedException
{
    /**
     * @var int
     */
    protected $code = 429;

    /**
     * @var string
     */
    protected $message = 'Too many requests.';

    protected string $title = '429 Too Many Requests';
    protected string $description = 'The client application has surpassed its rate limit, ' .
        'or number of requests they can send in a given period of time.';
}
