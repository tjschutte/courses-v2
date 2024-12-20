<?php

declare(strict_types=1);

namespace Galeas\Api\BoundedContext\Security\Session\Projection\UserWithUsername;

use Doctrine\ODM\MongoDB\DocumentManager;
use Galeas\Api\CommonException\ProjectionCannotRead;

class UserIdFromSignInUsername
{
    private DocumentManager $projectionDocumentManager;

    public function __construct(DocumentManager $projectionDocumentManager)
    {
        $this->projectionDocumentManager = $projectionDocumentManager;
    }

    /**
     * @throws ProjectionCannotRead
     */
    public function userIdFromSignInUsername(string $username): ?string
    {
        try {
            $userWithUsername = $this->projectionDocumentManager
                ->createQueryBuilder(UserWithUsername::class)
                ->field('lowercaseUsername')->equals(strtolower($username))
                ->field('verified')->equals(true)
                ->getQuery()
                ->getSingleResult()
            ;

            if ($userWithUsername instanceof UserWithUsername) {
                return $userWithUsername->getUserId();
            }

            if (null === $userWithUsername) {
                return null;
            }

            throw new \Exception();
        } catch (\Throwable $exception) {
            throw new ProjectionCannotRead($exception);
        }
    }
}
