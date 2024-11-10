<?php

namespace App\DataFixtures;

use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class MemberFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Génère les données de test pour les membres.
     */
    private function membersGenerator()
    {
        yield ['olivier@localhost', '123456', 'ROLE_USER'];
        yield ['slash@localhost', '123456', 'ROLE_ADMIN'];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->membersGenerator() as [$email, $plainPassword, $role]) {
            $member = new Member();
            $password = $this->hasher->hashPassword($member, $plainPassword);
            $member->setEmail($email);
            $member->setPassword($password);
            $member->setRoles([$role]);

            $manager->persist($member);

            $this->addReference("member_{$email}", $member);
        }

        $manager->flush();
    }
}
