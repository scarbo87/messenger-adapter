<?php

namespace Enqueue\MessengerAdapter\Service\Contract;

use Enqueue\MessengerAdapter\Classes\DTO\MessageFailLogDTO;
use Enqueue\MessengerAdapter\Entity\AbstractMessageFailLog;

interface MessageFailLogStorageInterface
{
    public function log(MessageFailLogDTO $dto);

    public function getByIdentifier($identifier): AbstractMessageFailLog;

    public function getEntity(): AbstractMessageFailLog;
}
