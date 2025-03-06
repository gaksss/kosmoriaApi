<?php

namespace App\Security\Voter;

use App\Entity\Marker;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class MarkerVoter extends Voter
{
    public const EDIT = 'MARKER_EDIT';
    public const DELETE = 'MARKER_DELETE';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Marker;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Marker $marker */
        $marker = $subject;

        return match ($attribute) {
            self::EDIT, self::DELETE => $this->isOwner($marker, $user),
            default => false,
        };
    }

    private function isOwner(Marker $marker, User $user): bool
    {
        return $marker->getCreatedBy() === $user;
    }
}
