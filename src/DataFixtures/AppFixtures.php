<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Billet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Generates initialization data for billets.
     * @return \Generator
     */
    private static function billetsGenerator()
    {
        yield ["Tunisie"];
        yield ["France"];
    }

    /**
     * Generates initialization data for albums.
     * @return \Generator
     */
    private static function albumGenerator()
    {
        yield ["Tunisie", "First Album"];
        yield ["France", "Second Album"];
    }

    public function load(ObjectManager $manager): void
    {
        // Create billets with placeholder albums (or with their actual album names)
        foreach (self::billetsGenerator() as [$pays]) {
            // Create a new Album with a generic or placeholder name
            $album = new Album();
            $album->setName("Placeholder Album for $pays");
            $manager->persist($album);

            // Create the corresponding Billet and set its Album
            $billet = $this->createBillet($pays, $album);
            $manager->persist($billet);
        }

        // Persist all billets and their albums
        $manager->flush();

        // Update the album names to the final values based on `albumGenerator`
        foreach (self::albumGenerator() as [$pays, $name]) {
            // Find the existing billet by its 'pays'
            $billet = $manager->getRepository(Billet::class)->findOneBy(['pays' => $pays]);

            if ($billet) {
                // Create a new Album entity with the correct name
                $album = new Album();
                $album->setName($name);
                $manager->persist($album);

                // Update the billet to use the new album
                $billet->setAlbum($album);
                $manager->persist($billet);
            }
        }

        // Final flush to save the updated entities
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
