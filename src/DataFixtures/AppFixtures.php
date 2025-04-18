<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Media;
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
        //USER
        $users = [];
        // user amdin ina zaoui
        $admin = new User();
        $admin->setEmail('ina@zaoui.com');
        $admin->setFirstName('Ina');
        $admin->setLastName('Zaoui');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // user with medias for delete user test with medias in cascade
        $userDeleteMedias = new User();
        $userDeleteMedias->setEmail('userdeletemedias@zaoui.com');
        $userDeleteMedias->setFirstName('userdeletemedias');
        $userDeleteMedias->setLastName('UserDeleteMedias');
        $userDeleteMedias->setRoles(['ROLE_USER']);
        $userDeleteMedias->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
        $manager->persist($userDeleteMedias);

        // user with medias but no ROLE_USER
        $userNoRole = new User();
        $userNoRole->setEmail('usernorole@zaoui.com');
        $userNoRole->setFirstName('usernorole');
        $userNoRole->setLastName('UserNoRole');
        $userNoRole->setRoles(['']);
        $userNoRole->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
        $manager->persist($userNoRole);

        // standard users 
        for ($i = 0; $i<20; $i++){
            $user = new User();
            $user->setEmail(sprintf('user' . $i . '@zaoui.com'));
            $user->setFirstName(sprintf('UserFirstName' . $i));
            $user->setLastName('UserLastName' . $i);
            $user->setDescription(sprintf('Description' . $i));
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        // ALBUM
        $albums = [];
        for ($i=1; $i<6; $i++){
            $album = new Album();
            $album->setName(sprintf('Album ' . $i));
            $manager->persist($album);
            $albums[] = $album;
        }

        // MEDIA
        // medias of userMedias
        for ($i=1; $i<10; $i++){
            $media = new Media();
            $media->setUser($userDeleteMedias);
            $media->setAlbum($albums[random_int(0, 4)]);
            $media->setPath(sprintf('uploads/000' . $i . '.jpg'));
            $media->setTitle(sprintf('Titre userDeleteMedias ' . $i));
            $manager->persist($media);
        }

        // medias of userNoRole
        for ($i=10; $i<20; $i++){
            $media = new Media();
            $media->setUser($userNoRole);
            $media->setAlbum($albums[random_int(0, 4)]);
            $media->setPath(sprintf('uploads/00' . $i . '.jpg'));
            $media->setTitle(sprintf('Titre userNoRole ' . $i));
            $manager->persist($media);
        }

        // medias with user and album
        for ($i=20; $i<100; $i++){
            $media = new Media();
            $media->setUser($users[random_int(0, count($users)-1)]);
            $media->setAlbum($albums[random_int(0, 4)]);
            $media->setPath(sprintf('uploads/00' . $i . '.jpg'));
            $media->setTitle(sprintf('Titre ' . $i));
            $manager->persist($media);
        }

        // medias with user without album
        for ($i=100; $i<200; $i++){
            $media = new Media();
            $media->setUser($users[random_int(0, count($users)-1)]);
            $media->setAlbum(null);
            $media->setPath(sprintf('uploads/00' . $i . '.jpg'));
            $media->setTitle(sprintf('Titre ' . $i));
            $manager->persist($media);
        }

        $manager->flush();
    }
}
