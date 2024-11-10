<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Billet;
use App\Entity\Exposition;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Génère les données de billets et albums.
     */
    private static function billetsAndAlbumsGenerator()
    {
        yield ["Tunisie", "First Album", "10 TND", "2022-01-01"];
        yield ["France", "Second Album", "20 EUR", "2020-05-12"];
    }

    /**
     * Génère les données d'exposition.
     */
    private function expositionsGenerator()
    {
        yield ['Exposition in Tunisia', true];
        yield ['Exposition in France', false];
    }

    public function load(ObjectManager $manager): void
    {
        $memberEmails = ['olivier@localhost', 'slash@localhost'];

        foreach ($memberEmails as $memberEmail) {
            // Récupérer le membre en utilisant la référence créée dans MemberFixtures
            /** @var Member $member */
            $member = $this->getReference("member_{$memberEmail}");

            // Créer un album pour chaque membre
            $album = new Album();
            $album->setName("Album de $memberEmail");
            $album->setMember($member);
            $manager->persist($album);

            // Créer des billets et les associer avec l'album
            $billets = [];
            foreach (self::billetsAndAlbumsGenerator() as $billetIndex => [$pays, $albumName, $valeur, $dateApparition]) {
                $billet = new Billet();
                $billet->setPays($pays);
                $billet->setValeur($valeur);
                $billet->setDateApparition($dateApparition);
                $billet->setAlbum($album);

                $manager->persist($billet);

                // Ajouter une référence pour utiliser le billet dans les expositions
                $this->addReference("billet_{$memberEmail}_{$billetIndex}", $billet);
                $billets[] = $billet;
            }

            // Créer des expositions et les associer avec les membres et billets
            foreach ($this->expositionsGenerator() as $expoIndex => [$description, $publiee]) {
                $exposition = new Exposition();
                $exposition->setDescription($description);
                $exposition->setPubliee($publiee);
                $exposition->setMember($member);

                // Associer les billets existants à l'exposition
                foreach ($billets as $billet) {
                    $exposition->addBillet($billet);
                }

                $manager->persist($exposition);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            MemberFixtures::class,
        ];
    }
}
