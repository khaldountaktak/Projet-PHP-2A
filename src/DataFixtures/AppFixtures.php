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

    private static function billetsAndAlbumsGenerator()
    {
        yield ["Tunisie", "First Album", "10 TND", "2022-01-01"];
        yield ["France", "Second Album", "20 EUR", "2020-05-12"];
    }

    private function membersGenerator()
    {
        yield ['olivier@localhost', '123456'];
        yield ['slash@localhost', '123456'];
    }

    private function expositionsGenerator()
    {
        yield ['Exposition in Tunisia', true];
        yield ['Exposition in France', false];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->membersGenerator() as $memberIndex => [$email, $plainPassword]) {
            // Création de chaque membre
            $user = new Member();
            $password = $this->hasher->hashPassword($user, $plainPassword);
            $user->setEmail($email);
            $user->setPassword($password);

            // Création de l'album pour le membre
            $album = new Album();
            $album->setName("Album de $email");
            $album->setMember($user);

            $manager->persist($user);
            $manager->persist($album);

            // Création des billets et association à l'album
            $billets = [];
            foreach (self::billetsAndAlbumsGenerator() as $billetIndex => [$pays, $albumName, $valeur, $dateApparition]) {
                $billet = new Billet();
                $billet->setPays($pays);
                $billet->setValeur($valeur);
                $billet->setDateApparition($dateApparition);
                $billet->setAlbum($album);

                $manager->persist($billet);

                // Sauvegarde des billets en tant que référence pour les expositions
                $this->addReference("billet_{$memberIndex}_{$billetIndex}", $billet);
                $billets[] = $billet;
            }

            // Création des expositions et association des billets via références
            foreach ($this->expositionsGenerator() as $expoIndex => [$description, $publiee]) {
                $exposition = new Exposition();
                $exposition->setDescription($description);
                $exposition->setPubliee($publiee);
                $exposition->setMember($user);

                // Associer des billets existants par références
                $exposition->addBillet($this->getReference("billet_{$memberIndex}_0"));
                $exposition->addBillet($this->getReference("billet_{$memberIndex}_1"));

                $manager->persist($exposition);
            }
        }

        $manager->flush();
    }
}
