<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Billet;
use App\Entity\Exposition;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Generates billets and albums data.
     */
    private static function billetsAndAlbumsGenerator()
    {
        yield ["Tunisie", "First Album", "10 TND", "2022-01-01"];
        yield ["France", "Second Album", "20 EUR", "2020-05-12"];
    }

    /**
     * Generates initialization data for members
     */
    private function membersGenerator()
    {
        yield ['olivier@localhost', '123456'];
        yield ['slash@localhost', '123456'];
    }

    /**
     * Generates exposition data.
     */
    private function expositionsGenerator()
    {
        yield ['Exposition in Tunisia', true];
        yield ['Exposition in France', false];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->membersGenerator() as $memberIndex => [$email, $plainPassword]) {
            // Step 1: Create each member and set their password
            $member = new Member();
            $password = $this->hasher->hashPassword($member, $plainPassword);
            $member->setEmail($email);
            $member->setPassword($password);

            // Step 2: Create an album for each member
            $album = new Album();
            $album->setName("Album de $email");
            $album->setMember($member);

            $manager->persist($member);
            $manager->persist($album);

            // Step 3: Create billets and associate them with the album
            $billets = [];
            foreach (self::billetsAndAlbumsGenerator() as $billetIndex => [$pays, $albumName, $valeur, $dateApparition]) {
                $billet = new Billet();
                $billet->setPays($pays);
                $billet->setValeur($valeur);
                $billet->setDateApparition($dateApparition);
                $billet->setAlbum($album);

                $manager->persist($billet);

                // Save billet as a reference for later use in expositions
                $this->addReference("billet_{$memberIndex}_{$billetIndex}", $billet);
                $billets[] = $billet;
            }

            // Step 4: Create expositions and associate them with the member and billets
            foreach ($this->expositionsGenerator() as $expoIndex => [$description, $publiee]) {
                $exposition = new Exposition();
                $exposition->setDescription($description);
                $exposition->setPubliee($publiee);
                $exposition->setMember($member);

                // Associate existing billets with the exposition
                foreach ($billets as $billet) {
                    $exposition->addBillet($billet);
                }

                $manager->persist($exposition);
            }
        }

        $manager->flush();
    }
}
