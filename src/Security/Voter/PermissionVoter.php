<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Role;
use App\Entity\SiteAssignment;
use App\Entity\User;
use App\Repository\SiteAssignmentRepository;
use App\Security\RbacAttributes;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, mixed>
 * @see docs/plan/RBAC_Reset.md
 */
final class PermissionVoter extends Voter
{
    public function __construct(
        private readonly SiteAssignmentRepository $siteAssignmentRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return '' !== trim($attribute) && str_contains($attribute, '.') && 'site.own' !== $attribute;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        if (in_array(Role::ADMIN, $token->getRoleNames(), true)) {
            return true;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $siteId = match (true) {
            is_int($subject) && $subject > 0 => $subject,
            is_string($subject) && ctype_digit($subject) && $subject > '0' => (int) $subject,
            default => 0,
        };

        if ($siteId > 0) {
            $assignment = $this->siteAssignmentRepository->findForUserAndSite($user, $siteId);
            if (!$assignment instanceof SiteAssignment) {
                return false;
            }

            return $this->assignmentAllows($assignment, $attribute);
        }

        // No site subject: any matching assignment may grant (e.g. site.list).
        foreach ($this->siteAssignmentRepository->findBy(['user' => $user]) as $assignment) {
            if ($this->assignmentAllows($assignment, $attribute)) {
                return true;
            }
        }

        return false;
    }

    private function assignmentAllows(SiteAssignment $assignment, string $attribute): bool
    {
        $role = $assignment->getRole();

        if ($role->isSiteAdmin()) {
            return RbacAttributes::isSiteInterior($attribute);
        }

        return $role->hasPermission($attribute);
    }
}
