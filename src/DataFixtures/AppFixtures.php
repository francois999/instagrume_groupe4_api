<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Incident;
use App\Entity\Type;
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

        $type1 = new Type();
        $type1->setLibelle('Accident de la route');
        $manager->persist($type1);

        $type2 = new Type();
        $type2->setLibelle('Voiture mal garée');
        $manager->persist($type2);

        $type3 = new Type();
        $type3->setLibelle('Voirie abîmée');
        $manager->persist($type3);

        $type4 = new Type();
        $type4->setLibelle('Autre');
        $manager->persist($type4);

        $incident1 = new Incident();
        $incident1->setLieu('Avenue des jambons');
        $incident1->setDescription('Deux voitures se sont rentrées dedans, la police est sur les lieux.');
        $date_input = new \DateTime();
        $date_input->setTimestamp(strtotime("2023/11/13 10:01:00"));
        $incident1->setDateProbleme($date_input);
        $incident1->setType($type1);
        $incident1->setAuteur($userStandard3);
        $manager->persist($incident1);

        $incident2 = new Incident();
        $incident2->setLieu('Rue de la presse');
        $incident2->setDescription('Une voiture a renversé un piéton puis a pris la fuite. Je reste auprès du piéton en attendant les secours.');
        $date_input = new \DateTime();
        $date_input->setTimestamp(strtotime("2023/11/12 11:03:00"));
        $incident2->setDateProbleme($date_input);
        $incident2->setType($type1);
        $incident2->setAuteur($userStandard1);
        $manager->persist($incident2);

        $incident3 = new Incident();
        $incident3->setLieu('Rue de la crème');
        $incident3->setDescription('La piste cyclable est encore encombrée par une voiture...');
        $date_input = new \DateTime();
        $date_input->setTimestamp(strtotime("2023/11/13 15:03:00"));
        $incident3->setDateProbleme($date_input);
        $incident3->setType($type2);
        $incident3->setAuteur($userStandard3);
        $manager->persist($incident3);

        $manager->flush();
    }
}
