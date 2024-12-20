<?php

declare(strict_types=1);

namespace Galeas\Api\BoundedContext\Identity\User\Projection\SentVerificationEmail;

use Doctrine\ODM\MongoDB\DocumentManager;
use Galeas\Api\BoundedContext\Identity\User\Event\PrimaryEmailVerificationCodeSent;
use Galeas\Api\Common\Event\Event;
use Galeas\Api\Service\QueueProcessor\EventProjector;

class SentVerificationEmailProjector extends EventProjector
{
    public function __construct(DocumentManager $projectionDocumentManager)
    {
        $this->projectionDocumentManager = $projectionDocumentManager;
    }

    protected function project(Event $event): void
    {
        if ($event instanceof PrimaryEmailVerificationCodeSent) {
            $this->saveOne(
                SentVerificationEmail::fromProperties(
                    $event->eventId()->id(),
                    $event->aggregateId()->id(),
                    $event->verificationCodeSent(),
                    $event->toEmailAddress(),
                    $event->emailContents(),
                    $event->fromEmailAddress(),
                    $event->subjectLine(),
                    $event->recordedOn()
                )
            );
        }
    }
}
