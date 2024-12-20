<?php

declare(strict_types=1);

namespace Tests\Galeas\Api\UnitAndIntegration\Util;

use Galeas\Api\BoundedContext\CreditCardProduct\Product\Event\ProductActivated;
use Galeas\Api\BoundedContext\CreditCardProduct\Product\Event\ProductDeactivated;
use Galeas\Api\BoundedContext\CreditCardProduct\Product\Event\ProductDefined;
use Galeas\Api\BoundedContext\Identity\TakenEmail\Event\AbandonedEmailRetaken;
use Galeas\Api\BoundedContext\Identity\TakenEmail\Event\EmailAbandoned;
use Galeas\Api\BoundedContext\Identity\TakenEmail\Event\EmailTaken;
use Galeas\Api\BoundedContext\Identity\User\Event\PrimaryEmailChangeRequested;
use Galeas\Api\BoundedContext\Identity\User\Event\PrimaryEmailVerificationCodeSent;
use Galeas\Api\BoundedContext\Identity\User\Event\PrimaryEmailVerified;
use Galeas\Api\BoundedContext\Identity\User\Event\SignedUp;
use Galeas\Api\BoundedContext\Security\Session\Event\SignedIn;
use Galeas\Api\BoundedContext\Security\Session\Event\SignedOut;
use Galeas\Api\BoundedContext\Security\Session\Event\TokenRefreshed;
use Galeas\Api\Common\Id\Id;

/**
 * The functions below are used in tests to avoid having to generate events over and over again.
 * It's recommended to not edit existing functions, because tests might make assumptions
 * using the results. At best, editing means you might break existing tests. And at worst, it means
 * that now you aren't testing certain things.
 *
 * There is a naming convention here, that should help us get different events for different use cases.
 * When we speak about another value (e.g., one more email) in the same aggregate, we refer to the new value
 * as "second" or "third" or "fourth" etc.
 * When we speak about another aggregate, or another value inside a separate aggregate, we prefix with "another".
 * Example:
 *  UserAggregate: SignIn with "email", Request change to a "second email".
 *  AnotherUserAggregate: SignIn with "another email", Request a change to "another second email".
 *
 * When there are tests you need to write that don't quite fit the sample events, don't try to pollute this class.
 * Instead, simply instantiate events directly in your test, the world won't end because of a few extra lines of code.
 */
abstract class SampleEvents
{
    public static function userEvents(): array
    {
        $event1 = self::signedUp();
        $event2 = self::primaryEmailVerificationCodeSent(
            $event1->aggregateId(),
            2,
            $event1->eventId(),
            $event1->eventId()
        );
        $event3 = self::primaryEmailVerified(
            $event1->aggregateId(),
            3,
            $event2->eventId(),
            $event1->eventId(),
        );
        $event4 = self::primaryEmailChangeRequested(
            $event1->aggregateId(),
            4,
            $event3->eventId(),
            $event1->eventId()
        );

        return [$event1, $event2, $event3, $event4];
    }

    public static function signedUp(): SignedUp
    {
        $eventId = Id::createNew();
        $aggregateId = Id::createNew();

        return SignedUp::new(
            $eventId,
            $aggregateId,
            1,
            $eventId,
            $eventId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            self::sampleEmail(),
            self::sampleVerificationCode(),
            self::sampleHashedPassword(),
            self::sampleUsername(),
            true,
        );
    }

    public static function primaryEmailVerificationCodeSent(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId,
    ): PrimaryEmailVerificationCodeSent {
        $eventId = Id::createNewByHashing('Identity/User/PrimaryEmailVerificationSent:'.$causationId->id());

        return PrimaryEmailVerificationCodeSent::new(
            $eventId,
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            self::sampleVerificationCode(),
            self::sampleEmail(),
            'Your code is: '.self::sampleVerificationCode(),
            self::systemEmailFrom(),
            'Verify Yourself'
        );
    }

    public static function primaryEmailVerified(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId,
    ): PrimaryEmailVerified {
        $eventId = Id::createNew();

        return PrimaryEmailVerified::new(
            $eventId,
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            self::sampleVerificationCode(),
        );
    }

    public static function primaryEmailChangeRequested(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId
    ): PrimaryEmailChangeRequested {
        $eventId = Id::createNew();

        return PrimaryEmailChangeRequested::new(
            $eventId,
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            self::secondSampleEmail(),
            self::secondSampleVerificationCode(),
            self::sampleHashedPassword()
        );
    }

    public static function primaryEmailChangeRequestedAgain(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId
    ): PrimaryEmailChangeRequested {
        $eventId = Id::createNew();

        return PrimaryEmailChangeRequested::new(
            $eventId,
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            self::thirdSampleEmail(),
            self::thirdSampleVerificationCode(),
            self::sampleHashedPassword()
        );
    }

    public static function primaryEmailChangeRequestedAgainAgain(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId
    ): PrimaryEmailChangeRequested {
        $eventId = Id::createNew();

        return PrimaryEmailChangeRequested::new(
            $eventId,
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            self::fourthSampleEmail(),
            self::fourthSampleVerificationCode(),
            self::sampleHashedPassword()
        );
    }

    public static function anotherSignedUp(): SignedUp
    {
        $eventId = Id::createNew();
        $aggregateId = Id::createNew();

        return SignedUp::new(
            $eventId,
            $aggregateId,
            1,
            $eventId,
            $eventId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::anotherSampleMetadata(null),
            self::anotherSampleEmail(),
            self::anotherSampleVerificationCode(),
            self::anotherSampleHashedPassword(),
            self::anotherSampleUsername(),
            true,
        );
    }

    public static function takenEmailEvents(): array
    {
        $emailTaken = self::emailTaken(
            'test1239jau@ambar.cloud',
            Id::createNew()
        );
        $emailAbandoned = self::emailAbandoned(
            $emailTaken->aggregateId(),
            2,
            $emailTaken->eventId(),
            $emailTaken->eventId()
        );
        $abandonedEmailRetaken = self::abandonedEmailRetaken(
            $emailTaken->aggregateId(),
            3,
            $emailAbandoned->eventId(),
            $emailTaken->eventId(),
            Id::createNew()
        );

        return [$emailTaken, $emailAbandoned, $abandonedEmailRetaken];
    }

    public static function emailTaken(string $email, Id $takenByUser): EmailTaken
    {
        $eventId = Id::createNew();
        $aggregateId = Id::createNewByHashing(
            'Identity_TakenEmail:'.strtolower($email)
        );

        return EmailTaken::new(
            $eventId,
            $aggregateId,
            1,
            $eventId,
            $eventId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            self::sampleEmail(),
            $takenByUser
        );
    }

    public static function emailAbandoned(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId
    ): EmailAbandoned {
        return EmailAbandoned::new(
            Id::createNew(),
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null)
        );
    }

    public static function abandonedEmailRetaken(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId,
        Id $retakenByUser
    ): AbandonedEmailRetaken {
        return AbandonedEmailRetaken::new(
            Id::createNew(),
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            $retakenByUser
        );
    }

    public static function sessionEvents(): array
    {
        $event1 = self::signedIn();
        $event2 = self::tokenRefreshed(
            $event1->aggregateId(),
            2,
            $event1->eventId(),
            $event1->eventId()
        );
        $event3 = self::signedOut(
            $event1->aggregateId(),
            3,
            $event2->eventId(),
            $event1->eventId()
        );

        return [$event1, $event2, $event3];
    }

    public static function signedIn(): SignedIn
    {
        $eventId = Id::createNew();
        $aggregateId = Id::createNew();
        $asUser = Id::createNew();

        return SignedIn::new(
            $eventId,
            $aggregateId,
            1,
            $eventId,
            $eventId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            $asUser,
            null,
            self::sampleEmail(),
            self::sampleHashedPassword(),
            self::sampleDeviceLabel(),
            self::sampleIp(),
            self::sampleSessionToken()
        );
    }

    public static function tokenRefreshed(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId
    ): TokenRefreshed {
        $eventId = Id::createNew();
        $existingSessionToken = self::sampleSessionToken();

        return TokenRefreshed::new(
            $eventId,
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata($existingSessionToken),
            self::secondSampleIp(),
            $existingSessionToken,
            self::secondSampleSessionToken()
        );
    }

    public static function signedOut(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId
    ): SignedOut {
        $eventId = Id::createNew();
        $existingSessionToken = self::secondSampleSessionToken();

        return SignedOut::new(
            $eventId,
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata($existingSessionToken),
            self::thirdSampleIp(),
            $existingSessionToken,
        );
    }

    public static function creditCardProductEvents(): array
    {
        $productDefined = self::productDefined();
        $productActivated = self::productActivated(
            $productDefined->aggregateId(),
            2,
            $productDefined->eventId(),
            $productDefined->eventId()
        );
        $productDeactivated = self::productDeactivated(
            $productDefined->aggregateId(),
            3,
            $productActivated->eventId(),
            $productDefined->eventId()
        );

        return [
            $productDefined,
            $productActivated,
            $productDeactivated,
        ];
    }

    public static function productDefined(): ProductDefined
    {
        $eventId = Id::createNew();
        $aggregateId = Id::createNew();

        return ProductDefined::new(
            $eventId,
            $aggregateId,
            1,
            $eventId,
            $eventId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null),
            'Cool-Card',
            1_200,
            5_000,
            'monthly',
            50_000,
            0,
            'none',
            '#7fffd4'
        );
    }

    public static function productActivated(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId
    ): ProductActivated {
        $eventId = Id::createNew();

        return ProductActivated::new(
            $eventId,
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null)
        );
    }

    public static function productDeactivated(
        Id $aggregateId,
        int $aggregateVersion,
        Id $causationId,
        Id $correlationId
    ): ProductDeactivated {
        $eventId = Id::createNew();

        return ProductDeactivated::new(
            $eventId,
            $aggregateId,
            $aggregateVersion,
            $causationId,
            $correlationId,
            new \DateTimeImmutable('2024-02-02 03:00:32'),
            self::sampleMetadata(null)
        );
    }

    private static function sampleMetadata(?string $withSessionToken): array
    {
        return [
            'someId' => Id::createNew()->id(),
            'environment' => 'native',
            'devicePlatform' => 'linux',
            'deviceModel' => 'Penguin 1.0',
            'deviceOSVersion' => 'Ubuntu 14.04',
            'deviceOrientation' => 'landscape',
            'latitude' => 12.321_23,
            'longitude' => 22.321_23,
            'ipAddress' => '120.123.193.12',
            'userAgent' => 'Test_UserAgent',
            'referer' => 'example.com',
            'withSessionToken' => $withSessionToken,
        ];
    }

    private static function anotherSampleMetadata(?string $withSessionToken): array
    {
        return [
            'someId' => Id::createNew()->id(),
            'environment' => 'browser',
            'devicePlatform' => 'windows',
            'deviceModel' => 'The OG',
            'deviceOSVersion' => 'Windows 99.9999',
            'deviceOrientation' => 'portrait',
            'latitude' => 15.323_2,
            'longitude' => -25.321_23,
            'ipAddress' => '150.102.12.3',
            'userAgent' => 'A_COOL_USER_AGENT',
            'referer' => '2.example.com',
            'withSessionToken' => $withSessionToken,
        ];
    }

    private static function sampleEmail(): string
    {
        return 'test@galeas.com';
    }

    private static function secondSampleEmail(): string
    {
        return 'proof@galeas2.net';
    }

    private static function thirdSampleEmail(): string
    {
        return 'proof123123@example-example.net';
    }

    private static function fourthSampleEmail(): string
    {
        return 'fourth@example-example.net';
    }

    private static function anotherSampleEmail(): string
    {
        return 'anotherEmail@gmail.com';
    }

    private static function systemEmailFrom(): string
    {
        return 'from@system.example.com';
    }

    private static function sampleHashedPassword(): string
    {
        return '$2y$10$tS8Y8CvwOeBVaFzPkXOfBuSearouW45pb5OlujqV6Y2BQPgvU5W2q'; // corresponds to "abcDEFg1/2"
    }

    private static function anotherSampleHashedPassword(): string
    {
        return '$2a$10$/q4ZluKn5QrNz2FizyFxaOtinBAfninfZTFAI/02d2kfHTcgTc336'; // corresponds to "b3rdsnn128FU&d9"
    }

    private static function sampleUsername(): string
    {
        return 'MyUsername';
    }

    private static function anotherSampleUsername(): string
    {
        return 'ThisIsMe';
    }

    private static function sampleVerificationCode(): string
    {
        return 'FirstVerificationCode';
    }

    private static function secondSampleVerificationCode(): string
    {
        return 'SecondVerificationCode';
    }

    private static function thirdSampleVerificationCode(): string
    {
        return 'ThirdVerificationCode';
    }

    private static function fourthSampleVerificationCode(): string
    {
        return 'FourthVerificationCode';
    }

    private static function anotherSampleVerificationCode(): string
    {
        return 'FirstVerificationCode';
    }

    private static function sampleDeviceLabel(): string
    {
        return 'My Iphone Device Label';
    }

    private static function sampleIp(): string
    {
        return '130.130.130.130';
    }

    private static function secondSampleIp(): string
    {
        return '131.131.131.131';
    }

    private static function thirdSampleIp(): string
    {
        return '132.132.132.132';
    }

    private static function sampleSessionToken(): string
    {
        return 'SessionToken17891028561029';
    }

    private static function secondSampleSessionToken(): string
    {
        return 'SessionToken02067776337012';
    }
}
