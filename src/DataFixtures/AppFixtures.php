<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Billet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Generer des billets et des albums.
     * 
     * @return \Generator
     */
    private static function billetsAndAlbumsGenerator()
    {
        yield ["Tunisie", "First Album", "10 TND", "2022-01-01"];
        yield ["France", "Second Album", "20 EUR", "2020-05-12"];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::billetsAndAlbumsGenerator() as [$pays, $albumName, $valeur, $dateApparition]) {

            $album = new Album();
            $album->setName($albumName);
            $manager->persist($album);

            $billet = $this->createBillet($pays, $valeur, $dateApparition, $album);
            $manager->persist($billet);
        }

        $manager->flush();
    }

    /**
     * Create a Billet entity.
     * 
     * @param string $pays
     * @param string $valeur
     * @param string $dateApparition
     * @param Album $album
     * @return Billet
     */
    private function createBillet(string $pays, string $valeur, string $dateApparition, Album $album): Billet
    {
        $billet = new Billet();
        $billet->setPays($pays);
        $billet->setValeur($valeur);
        $billet->setDateApparition($dateApparition);
        $billet->setAlbum($album);

        return $billet;
    }
}
