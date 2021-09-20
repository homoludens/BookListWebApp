<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UserFixtures extends Fixture
{
     private $passwordHasher;

     public function __construct(UserPasswordHasherInterface $passwordHasher)
     {
         $this->passwordHasher = $passwordHasher;
     }

    public function load(ObjectManager $manager)
    {
      $user = new User();
      $user->setEmail("test" . uniqid() . "@droopia.net");
      $user->setPassword($this->passwordHasher->hashPassword( $user, 'the_new_password'));
      $manager->persist($user);
      $manager->flush();

      // other fixtures can get this object using the UserFixtures::ADMIN_USER_REFERENCE constant
//      $this->addReference(self::ADMIN_USER_REFERENCE, $user);

    }

}
