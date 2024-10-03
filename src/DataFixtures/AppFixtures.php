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
     * 
     * @return \Generator
     */
    private static function billetsAndAlbumsGenerator()
    {
        yield ["Tunisie", "First Album"];
        yield ["France", "Second Album"];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::billetsAndAlbumsGenerator() as [$pays, $albumName]) {

            $album = new Album();
            $album->setName($albumName);
            $manager->persist($album);

            $billet = $this->createBillet($pays, $album);
            $manager->persist($billet);
        }

        // Persist all billets and albums in one go
        $manager->flush();
    }

    /**
     * Create a Billet entity.
     * 
     * @param string $pays
     * @param Album $album
     * @return Billet
     */
    private function createBillet(string $pays, Album $album): Billet
    {
        $billet = new Billet();
        $billet->setPays($pays);
        $billet->setAlbum($album);

        return $billet;
    }
}
