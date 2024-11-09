<?php

declare(strict_types=1);

namespace Galeas\Api\BoundedContext\CreditCardProduct\Product\CommandHandler;

use Galeas\Api\BoundedContext\CreditCardProduct\Product\Command\DefineProductCommand;
use Galeas\Api\BoundedContext\CreditCardProduct\Product\Event\InvalidPaymentCycle;
use Galeas\Api\BoundedContext\CreditCardProduct\Product\Event\InvalidReward;
use Galeas\Api\BoundedContext\CreditCardProduct\Product\Event\ProductDefined;
use Galeas\Api\Common\Id\Id;
use Galeas\Api\CommonException\EventStoreCannotRead;
use Galeas\Api\CommonException\EventStoreCannotWrite;
use Galeas\Api\Primitive\PrimitiveCreation\NoRandomnessAvailable;
use Galeas\Api\Service\EventStore\EventStore;

class DefineProductHandler
{
    private EventStore $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * @throws EventStoreCannotRead|EventStoreCannotWrite|NoRandomnessAvailable
     * @throws InvalidPaymentCycle|InvalidReward
     */
    public function handle(DefineProductCommand $command): void
    {
        $eventId = Id::createNewByHashing($command->productIdentifierForAggregateIdHash);

        $this->eventStore->beginTransaction();
        $alreadyDefined = null !== $this->eventStore->findEvent($eventId->id());

        if ($alreadyDefined) {
            $this->eventStore->completeTransaction();

            return;
        }

        if (!\in_array($command->paymentCycle, ['monthly', 'quarterly'], true)) {
            throw new InvalidPaymentCycle();
        }

        if (!\in_array($command->reward, ['points', 'cashback', 'no_reward', 'none'], true)) {
            throw new InvalidReward();
        }

        $aggregateId = Id::createNew();
        $event = ProductDefined::new(
            $eventId,
            $aggregateId,
            1,
            $eventId,
            $eventId,
            new \DateTimeImmutable('now'),
            [],
            $command->name,
            $command->interestInBasisPoints,
            $command->annualFeeInCents,
            $command->paymentCycle,
            $command->creditLimitInCents,
            $command->maxBalanceTransferAllowedInCents,
            $command->reward,
            $command->cardBackgroundHex
        );

        $this->eventStore->save($event);
        $this->eventStore->completeTransaction();
    }
}