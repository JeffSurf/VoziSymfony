<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Avis;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer les utilisateurs
        $utilisateurs = [];
        for($i = 0; $i < 10; ++$i)
        {
            $utilisateur = new Utilisateur();

            $utilisateur->setNom($faker->name);
            $utilisateur->setPrenom($faker->firstNameMale);
            $utilisateur->setPseudo($faker->userName);
            $utilisateur->setEmail($faker->email);
            $utilisateur->setPassword($this->userPasswordHasher->hashPassword($utilisateur, $faker->password));

            $utilisateurs[] = $utilisateur;

            $manager->persist($utilisateur);
        }

        //Créer les apps
        $applis = [];
        for($i = 0; $i < 10; ++$i)
        {
            $appli = new Application();

            $appli->setNom($faker->company);
            $appli->setPort($faker->numberBetween(0, 9999));
            $appli->setIp($faker->ipv4);
            $appli->setDescription($faker->text);
            $appli->setUrl($faker->url);
            $appli->setUtilisateur($utilisateurs[random_int(0, sizeof($utilisateurs) - 1)]);

            $applis[] = $appli;

            $manager->persist($appli);

        }


        //Créer des avis
        for($i = 0; $i < 16; ++$i)
        {
            $avis = new Avis();

            $avis->setMessage($faker->realText());
            $avis->setApplication($applis[random_int(0, sizeof($applis) - 1)]);
            $avis->setUtilisateur($utilisateurs[random_int(0, sizeof($utilisateurs) - 1)]);

            $manager->persist($avis);
        }


        $manager->flush();
    }
}
