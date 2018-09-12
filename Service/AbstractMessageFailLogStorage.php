<?php

namespace Enqueue\MessengerAdapter\Service;

use Doctrine\ORM\EntityManagerInterface;
use Enqueue\MessengerAdapter\Classes\DTO\MessageFailLogDTO;
use Enqueue\MessengerAdapter\Entity\AbstractMessageFailLog;
use Enqueue\MessengerAdapter\Exception\ExpectedMessageLogGotAnother;
use Enqueue\MessengerAdapter\Exception\MessageLogIsNotExisted;
use Enqueue\MessengerAdapter\Service\Contract\MessageFailLogStorageInterface;

abstract class AbstractMessageFailLogStorage implements MessageFailLogStorageInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * MessageFailLogStorage constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param MessageFailLogDTO $dto
     */
    public function log(MessageFailLogDTO $dto): void
    {
        $entity = $this->getEntity();

        if (!($entity instanceof AbstractMessageFailLog)) {
            throw new ExpectedMessageLogGotAnother(sprintf('Class %s is not instanceof AbstractMessageFailLog', \get_class($entity)));
        }

        $entity->setQueueName($dto->getQueueName())
            ->setAttempt($dto->getAttempt())
            ->setLimit($dto->getLimit())
            ->setEventType($dto->getEventType());

        $message = $dto->getMessage();
        if ($message) {
            $entity->setMessageBody($message->getBody())
                ->setMessageHeaders($message->getHeaders())
                ->setMessageProperties($message->getProperties());
        }

        $envelope = $dto->getEnvelope();
        if ($envelope) {
            $entity->setEnvelope(\serialize($envelope));
        }

        $exception = $dto->getException();
        if ($exception) {
            $entity
                ->setExceptionClass(\get_class($exception))
                ->setExceptionMessage($exception->getMessage())
                ->setExceptionTrace($exception->getTraceAsString());
        }

        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * @param $identifier
     *
     * @return AbstractMessageFailLog
     */
    public function getByIdentifier($identifier): AbstractMessageFailLog
    {
        /** @var AbstractMessageFailLog $entity */
        $entity = $this->em
            ->getRepository(\get_class($this->getEntity()))
            ->find($identifier);

        if (!$entity) {
            throw new MessageLogIsNotExisted(sprintf('Identifier %s is not present in database', $identifier));
        }

        return $entity;
    }

    /**
     * @return AbstractMessageFailLog
     */
    abstract public function getEntity(): AbstractMessageFailLog;
}
