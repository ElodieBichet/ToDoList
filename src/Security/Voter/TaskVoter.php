<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['TASK_EDIT', 'TASK_DELETE', 'TASK_TOGGLE'])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($attribute == 'TASK_TOGGLE') {
            return $user === $subject->getAuthor() || in_array('ROLE_ADMIN', $user->getRoles());
        }

        // ... (check conditions and return true to grant permission) ...
        if (in_array($attribute, ['TASK_EDIT', 'TASK_DELETE'])) {
            return ($user === $subject->getAuthor()) || (in_array('ROLE_ADMIN', $user->getRoles()) && null === $subject->getAuthor());
        }

        return false;
    }
}
