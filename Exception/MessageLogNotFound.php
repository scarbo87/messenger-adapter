<?php

namespace Enqueue\MessengerAdapter\Exception;

use Symfony\Component\Messenger\Exception\ExceptionInterface;

class MessageLogNotFound extends \RuntimeException implements ExceptionInterface
{
}
