<?php

declare(strict_types=1);

namespace Galeas\Api\BoundedContext\Security\Session\Projection\UserWithEmail;

use Doctrine\ODM\MongoDB\DocumentManager;
use Galeas\Api\BoundedContext\Identity\User\Event\PrimaryEmailChangeRequested;
use Galeas\Api\BoundedContext\Identity\User\Event\PrimaryEmailVerified;
use Galeas\Api\BoundedContext\Identity\User\Event\SignedUp;
use Galeas\Api\Common\Event\Event;
use Galeas\Api\Service\QueueProcessor\EventProjector;

class UserWithEmailProjector extends EventProjector
{
    public function __construct(DocumentManager $projectionDocumentManager)
    {
        $this->projectionDocumentManager = $projectionDocumentManager;
    }

    protected function project(Event $event): void
    {
        switch (true) {
            case $event instanceof SignedUp:
                $this->saveOne(UserWithEmail::fromUserIdAndEmails(
                    $event->aggregateId()->id(),
                    null,
                    $event->primaryEmail()
                ));

                break;

            case $event instanceof PrimaryEmailVerified:
                $userWithEmail = $this->getOne(UserWithEmail::class, ['id' => $event->aggregateId()->id()]);
                $this->saveOne($userWithEmail?->verifyEmail());

                break;

            case $event instanceof PrimaryEmailChangeRequested:
                $userWithEmail = $this->getOne(UserWithEmail::class, ['id' => $event->aggregateId()->id()]);
                $this->saveOne($userWithEmail?->requestNewEmail($event->newEmailRequested()));

                break;

            default:
                break;
        }
    }
}
