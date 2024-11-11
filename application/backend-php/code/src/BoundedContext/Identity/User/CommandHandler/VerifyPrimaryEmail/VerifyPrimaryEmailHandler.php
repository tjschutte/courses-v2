<?php

declare(strict_types=1);

namespace Galeas\Api\BoundedContext\Identity\User\CommandHandler\VerifyPrimaryEmail;

use Galeas\Api\BoundedContext\Identity\User\Aggregate\User;
use Galeas\Api\BoundedContext\Identity\User\Command\VerifyPrimaryEmail;
use Galeas\Api\BoundedContext\Identity\User\CommandHandler\RequestPrimaryEmailChange\RequestPrimaryEmailChangeHandler;
use Galeas\Api\BoundedContext\Identity\User\CommandHandler\SignUp\SignUpHandler;
use Galeas\Api\BoundedContext\Identity\User\Event\PrimaryEmailVerified;
use Galeas\Api\BoundedContext\Identity\User\Projection\PrimaryEmailVerificationCode\UserIdFromPrimaryEmailVerificationCode;
use Galeas\Api\BoundedContext\Identity\User\ValueObject\UnverifiedEmail;
use Galeas\Api\BoundedContext\Identity\User\ValueObject\VerifiedButRequestedNewEmail;
use Galeas\Api\BoundedContext\Identity\User\ValueObject\VerifiedEmail;
use Galeas\Api\Common\Id\Id;
use Galeas\Api\CommonException\EventStoreCannotRead;
use Galeas\Api\CommonException\EventStoreCannotWrite;
use Galeas\Api\CommonException\ProjectionCannotRead;
use Galeas\Api\Primitive\PrimitiveCreation\NoRandomnessAvailable;
use Galeas\Api\Service\EventStore\EventStore;

class VerifyPrimaryEmailHandler
{
    private EventStore $eventStore;

    private UserIdFromPrimaryEmailVerificationCode $userIdFromVerificationCode;

    public function __construct(
        EventStore $eventStore,
        UserIdFromPrimaryEmailVerificationCode $userIdFromVerificationCode
    ) {
        $this->eventStore = $eventStore;
        $this->userIdFromVerificationCode = $userIdFromVerificationCode;
    }

    /**
     * There is no need to check if the existing requested email is taken, as there must have been a check on it previously.
     *
     * @see SignUpHandler
     * @see RequestPrimaryEmailChangeHandler
     *
     * @throws EmailIsAlreadyVerified|NoVerifiableUserFoundForCode|VerificationCodeDoesNotMatch
     * @throws EventStoreCannotRead|EventStoreCannotWrite|NoRandomnessAvailable|ProjectionCannotRead
     */
    public function handle(VerifyPrimaryEmail $command): void
    {
        $userId = $this->userIdFromVerificationCode->userIdFromPrimaryEmailVerificationCode($command->verificationCode);

        if (null === $userId) {
            throw new NoVerifiableUserFoundForCode();
        }

        $this->eventStore->beginTransaction();

        $result = $this->eventStore->findAggregateAndEventIdsInLastEvent($userId);
        if (null === $result) {
            throw new NoVerifiableUserFoundForCode();
        }

        [$user, $lastEventId, $lastEventCorrelationId] = [$result->aggregate(), $result->eventIdInLastEvent(), $result->correlationIdInLastEvent()];
        if (!$user instanceof User) {
            throw new NoVerifiableUserFoundForCode();
        }

        if ($user->primaryEmailStatus() instanceof VerifiedEmail) {
            throw new EmailIsAlreadyVerified();
        }

        if (
            $user->primaryEmailStatus() instanceof UnverifiedEmail
            && $command->verificationCode !== $user->primaryEmailStatus()->verificationCode()->verificationCode()
        ) {
            throw new VerificationCodeDoesNotMatch();
        }

        if (
            $user->primaryEmailStatus() instanceof VerifiedButRequestedNewEmail
            && $command->verificationCode !== $user->primaryEmailStatus()->verificationCode()->verificationCode()
        ) {
            throw new VerificationCodeDoesNotMatch();
        }

        $event = PrimaryEmailVerified::new(
            Id::createNew(),
            $user->aggregateId(),
            $user->aggregateVersion() + 1,
            $lastEventId,
            $lastEventCorrelationId,
            new \DateTimeImmutable('now'),
            $command->metadata,
            $command->verificationCode
        );

        $this->eventStore->save($event);
        $this->eventStore->completeTransaction();
    }
}
