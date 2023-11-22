<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Like;
use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture {

    private $passwordHasher;

    public  function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void {
        $userStandard1 = new User();
        $userStandard1->setUserName('olivier');
        $userStandard1->setRoles(['ROLE_USER']);
        $userStandard1->setPassword($this->passwordHasher->hashPassword($userStandard1, 'password'));
        $manager->persist($userStandard1);

        $userStandard2 = new User();
        $userStandard2->setUserName('jean');
        $userStandard2->setRoles(['ROLE_USER']);
        $userStandard2->setPassword($this->passwordHasher->hashPassword($userStandard2, 'password'));
        $manager->persist($userStandard2);

        $userStandard3 = new User();
        $userStandard3->setUserName('artemis');
        $userStandard3->setRoles(['ROLE_USER']);
        $userStandard3->setPassword($this->passwordHasher->hashPassword($userStandard3, 'password'));
        $manager->persist($userStandard3);

        $userAdmin = new User();
        $userAdmin->setUserName('admin');
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, 'password'));
        $manager->persist($userAdmin);

        $comment1 = new Commentaire();
        $comment1->setValeur('Ceci est un commentaire 1.');
        $comment1->setUser($userStandard1);
        $manager->persist($comment1);

        $comment2 = new Commentaire();
        $comment2->setValeur('Ceci est un commentaire 2.');
        $comment2->setUser($userStandard2);
        $manager->persist($comment2);

        $manager->flush();

    }
}
