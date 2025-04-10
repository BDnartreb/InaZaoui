<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        //USERS
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setName('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setAdmin(true);
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));

        $manager->persist($admin);
        $this->addReference('admin-user', $admin); // utile pour d'autres fixtures/tests


        // MEDIA
        

        
        
        
        
        
        $manager->flush();
    }
}
