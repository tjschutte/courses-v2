<?php

declare(strict_types=1);

namespace Tests\Galeas\Api\UnitAndIntegration\BoundedContext\Identity\User\Projection;

use Galeas\Api\BoundedContext\Identity\User\Projection\SentVerificationEmail\ListSentVerificationEmail;
use Galeas\Api\BoundedContext\Identity\User\Projection\SentVerificationEmail\SentVerificationEmailProjector;
use Galeas\Api\Common\Id\Id;
use PHPUnit\Framework\Assert;
use Tests\Galeas\Api\UnitAndIntegration\ResetsEventStoreAndProjectionsIntegrationTest;
use Tests\Galeas\Api\UnitAndIntegration\Util\SampleEvents;

class SentVerificationEmailTest extends ResetsEventStoreAndProjectionsIntegrationTest
{
    public function testEvents()
    {
        $sentVerificationEmailProjector = $this->getContainer()
            ->get(SentVerificationEmailProjector::class)
        ;
        $listVerificationEmails = $this->getContainer()
            ->get(ListSentVerificationEmail::class);

        $primaryEmailVerificationCodeSent = SampleEvents::primaryEmailVerificationCodeSent(
            Id::createNew(),
            33,
            Id::createNew(),
            Id::createNew(),
        );
        $sentVerificationEmailProjector->project($primaryEmailVerificationCodeSent);
        Assert::assertEquals(
            [[
                'eventId' => $primaryEmailVerificationCodeSent->eventId()->id(),
                'userId' => $primaryEmailVerificationCodeSent->aggregateId()->id(),
                'verificationCodeSent' => $primaryEmailVerificationCodeSent->verificationCodeSent(),
                'toEmailAddress' => $primaryEmailVerificationCodeSent->toEmailAddress(),
                'emailContents' => $primaryEmailVerificationCodeSent->emailContents(),
                'fromEmailAddress' => $primaryEmailVerificationCodeSent->fromEmailAddress(),
                'subjectLine' => $primaryEmailVerificationCodeSent->subjectLine(),
                'recordedOn' => $primaryEmailVerificationCodeSent->recordedOn(),
            ]],
            $listVerificationEmails->list($primaryEmailVerificationCodeSent->eventId()->id())
        );
    }

}