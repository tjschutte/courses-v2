<?php

declare(strict_types=1);

namespace Galeas\Api\BoundedContext\Security\Session\Projection\UserWithEmail;

use Doctrine\ODM\MongoDB\DocumentManager;
use Galeas\Api\CommonException\ProjectionCannotRead;

class UserIdFromSignInEmail
{
    private DocumentManager $projectionDocumentManager;

    public function __construct(DocumentManager $projectionDocumentManager)
    {
        $this->projectionDocumentManager = $projectionDocumentManager;
    }

    /**
     * @throws ProjectionCannotRead
     */
    public function userIdFromSignInEmail(string $email): ?string
    {
        try {
            $queryBuilder = $this->projectionDocumentManager
                ->createQueryBuilder(UserWithEmail::class)
            ;

            $queryBuilder->addOr(
                $queryBuilder->expr()->addAnd(
                    $queryBuilder->expr()
                        ->field('status')
                        ->equals(Unverified::setStatus()),
                    $queryBuilder->expr()
                        ->field('canonicalRequestedEmail')
                        ->equals(strtolower($email))
                ),
                $queryBuilder->expr()->addAnd(
                    $queryBuilder->expr()
                        ->field('status')
                        ->equals(Verified::setStatus()),
                    $queryBuilder->expr()
                        ->field('canonicalVerifiedEmail')
                        ->equals(strtolower($email))
                ),
                $queryBuilder->expr()->addAnd(
                    $queryBuilder->expr()
                        ->field('status')
                        ->equals(RequestedChange::setStatus()),
                    $queryBuilder->expr()
                        ->field('canonicalVerifiedEmail')
                        ->equals(strtolower($email))
                )
            );

            $userWithEmail = $queryBuilder->getQuery()->getSingleResult();

            if ($userWithEmail instanceof UserWithEmail) {
                return $userWithEmail->getUserId();
            }

            if (null === $userWithEmail) {
                return null;
            }

            throw new \Exception();
        } catch (\Throwable $exception) {
            throw new ProjectionCannotRead($exception);
        }
    }
}