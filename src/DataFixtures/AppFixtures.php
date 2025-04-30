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

    private UserPasswordHasherInterface $userPasswordHasher;

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
        $admin->setDescription('Je suis Ina Zaoui');
        $manager->persist($admin);

        // user with medias for delete user test with medias in cascade
        $userLambda = new User();
        $userLambda->setEmail('userlambda@zaoui.com');
        $userLambda->setFirstName('userlambdaFirstName');
        $userLambda->setLastName('userlambdaLastName');
        $userLambda->setRoles(['ROLE_USER']);
        $userLambda->setPassword($this->userPasswordHasher->hashPassword($userLambda, 'password'));
        $userLambda->setDescription('Je suis le user lambda et je suis fier de représenter Monsieur tout le monde');
        $manager->persist($userLambda);

        // user with medias for delete user test with medias in cascade
        $userDeleted = new User();
        $userDeleted->setEmail('userdeleted@zaoui.com');
        $userDeleted->setFirstName('userdeletedFirstName');
        $userDeleted->setLastName('userdeletedLastName');
        $userDeleted->setRoles(['ROLE_USER']);
        $userDeleted->setPassword($this->userPasswordHasher->hashPassword($userDeleted, 'password'));
        $userDeleted->setDescription('Je suis le user delete et je suis destiné à être supprimé. Snif!');
        $manager->persist($userDeleted);

        // user with medias but no ROLE_USER
        $userFrozen = new User();
        $userFrozen->setEmail('userfrozen@zaoui.com');
        $userFrozen->setFirstName('userfrozenFirstName');
        $userFrozen->setLastName('userfrozenLastName');
        $userFrozen->setRoles(['ROLE_FROZEN']);
        $userFrozen->setPassword($this->userPasswordHasher->hashPassword($userFrozen, 'password'));
        $userFrozen->setDescription('Je suis le user frozen et je suis temporairement indésirable sur ce site.');
        $manager->persist($userFrozen);

        // standard users 
        for ($i = 0; $i<20; $i++){
            $user = new User();
            $user->setEmail(sprintf('user' . $i . '@zaoui.com'));
            $user->setFirstName(sprintf('UserFirstName' . $i));
            $user->setLastName('UserLastName' . $i);
            $user->setDescription(sprintf('Description' . $i));
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
            $user->setDescription('Je suis un user standard.');
            $manager->persist($user);
            $users[] = $user;
        }

        // ALBUM
        $albums = [];
        for ($i=1; $i<4; $i++){
            $album = new Album();
            $album->setName(sprintf('Album ' . $i));
            $manager->persist($album);
            $albums[] = $album;
        }

        $albumDelete = new Album();
        $albumDelete->setName(sprintf('Album Deleted'));
        $manager->persist($albumDelete);

        $albumFrozen = new Album();
        $albumFrozen->setName(sprintf('Album Frozen'));
        $manager->persist($albumFrozen);

        // MEDIA
        // medias of userlambda in album random
        for ($i=1; $i<10; $i++){
            $media = new Media();
            $media->setUser($userLambda);
            $media->setAlbum($albums[random_int(0, 2)]);
            $media->setPath(sprintf('uploads/000' . $i . '.jpg'));
            $media->setTitle(sprintf('Titre userLambda ' . $i));
            $manager->persist($media);
        }

        // medias of userDeleted in album random (to test delete of medias of a deleted user)
        for ($i=10; $i<20; $i++){
            $media = new Media();
            $media->setUser($userDeleted);
            $media->setAlbum($albums[random_int(0, 2)]);
            $media->setPath(sprintf('uploads/00' . $i . '.jpg'));
            $media->setTitle(sprintf('Titre userDeleted ' . $i));
            $manager->persist($media);
        }

        // medias in albumDelete (to test no delete of medias if album deleted)
        for ($i=20; $i<30; $i++){
            $media = new Media();
            $media->setUser($users[random_int(0, count($users)-1)]);
            $media->setAlbum($albumDelete);
            $media->setPath(sprintf('uploads/00' . $i . '.jpg'));
            $media->setTitle(sprintf('Titre albumDeleteMedia' . $i));
            $manager->persist($media);
        }

        // medias of userFrozen in a frozen album (to test if media of a frozen user are displayed)
        for ($i=30; $i<40; $i++){
            $media = new Media();
            $media->setUser($userFrozen);
            $media->setAlbum($albumFrozen);
            $media->setPath(sprintf('uploads/00' . $i . '.jpg'));
            $media->setTitle(sprintf('Titre userFrozen ' . $i));
            $manager->persist($media);
        }

        // medias album random
        for ($i=40; $i<100; $i++){
            $media = new Media();
            $media->setUser($users[random_int(0, count($users)-1)]);
            $media->setAlbum($albums[random_int(0, 2)]);
            $media->setPath(sprintf('uploads/00' . $i . '.jpg'));
            $media->setTitle(sprintf('Titre ' . $i));
            $manager->persist($media);
        }

        // medias without album
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
