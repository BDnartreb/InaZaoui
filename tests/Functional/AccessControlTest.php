<?php

namespace App\Tests\Functional;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccessControlTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->em = $container->get('doctrine')->getManager(); 
    }

    /**
    * 
    * @dataProvider provideAdminAccess
    */
    public function testAccess(string $email, string $path, string $codeHttp): void
    {
        
        if ($path === "/admin/album/update/id"){
            $albumId = $this->em->getRepository(Album::class)->findOneBy(['name' => 'Album 2'])->getId();
            $path = "/admin/album/update/" . $albumId;
        }

        if ($path === "/admin/album/delete/id"){
            $albumId = $this->em->getRepository(Album::class)->findOneBy(['name' => 'Album 2'])->getId();
            $path = "/admin/album/delete/" . $albumId;
        }

        if ($path === "/admin/media/delete/id"){
            $mediaId = $this->em->getRepository(Media::class)->findOneBy(['title' => 'Titre 40'])->getId();
            $path = "/admin/media/delete/" . $mediaId;
        }

        if ($path === "/admin/guest/update/id"){
            $userId = $this->em->getRepository(User::class)->findOneBy(['email' => 'user10@zaoui.com'])->getId();
            $path = "/admin/guest/update/" . $userId;
        }

        if ($path === "/admin/guest/delete/id"){
            $userId = $this->em->getRepository(User::class)->findOneBy(['email' => 'user10@zaoui.com'])->getId();
            $path = "/admin/guest/delete/" . $userId;
        }

        if ($path === "/guest/media/delete/id"){
            $mediaId = $this->em->getRepository(Media::class)->findOneBy(['title' => 'Titre 41'])->getId();
            $path = "/guest/media/delete/" . $mediaId;
        }

        if ($path === "/guest/media/delete/idbis"){
            $mediaId = $this->em->getRepository(Media::class)->findOneBy(['title' => 'Titre userLambda 3'])->getId();
            $path = "/guest/media/delete/" . $mediaId;
        }
        

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);
        $this->client->loginUser($user);
        $this->client->request('GET', $path);
        $this->assertResponseStatusCodeSame(constant(Response::class . '::' . $codeHttp));
    }

    /**
    * @return array<array{string, string, string}>
    */
    public static function provideAdminAccess(): array
    {
        return [            
            '/admin/album as admin user' => ["ina@zaoui.com", "/admin/album", "HTTP_OK"],
            '/admin/album as standard user' => ["userlambda@zaoui.com", "/admin/album", "HTTP_FORBIDDEN"],
            '/admin/album as frozen user' => ["userfrozen@zaoui.com", "/admin/album", "HTTP_FORBIDDEN"],

            '/admin/album/add as admin user' => ["ina@zaoui.com", "/admin/album/add", "HTTP_OK"],
            '/admin/album/add as standard user' => ["userlambda@zaoui.com", "/admin/album/add", "HTTP_FORBIDDEN"],
            '/admin/album/add as frozen user' => ["userfrozen@zaoui.com", "/admin/album/add", "HTTP_FORBIDDEN"],

            '/admin/album/update/id as admin user' => ["ina@zaoui.com", "/admin/album/update/id", "HTTP_OK"],
            '/admin/album/update/1 as standard user' => ["userlambda@zaoui.com", "/admin/album/update/1", "HTTP_FORBIDDEN"],
            '/admin/album/update/1 as frozen user' => ["userfrozen@zaoui.com", "/admin/album/update/1", "HTTP_FORBIDDEN"],
               
            '/admin/album/delete/id as admin user' => ["ina@zaoui.com", "/admin/album/delete/id", "HTTP_FOUND"],
            '/admin/album/delete/1 as standard user' => ["userlambda@zaoui.com", "/admin/album/delete/1", "HTTP_FORBIDDEN"],
            '/admin/album/delete/1 as frozen user' => ["userfrozen@zaoui.com", "/admin/album/delete/1", "HTTP_FORBIDDEN"], 


            '/admin/media as admin user' => ["ina@zaoui.com", "/admin/media", "HTTP_OK"],
            '/admin/media as standard user' => ["userlambda@zaoui.com", "/admin/media", "HTTP_FORBIDDEN"],
            '/admin/media as forzen user' => ["userfrozen@zaoui.com", "/admin/media", "HTTP_FORBIDDEN"],

            '/admin/media/add as admin user' => ["ina@zaoui.com", "/admin/media/add", "HTTP_OK"],
            '/admin/media/add as standard user' => ["userlambda@zaoui.com", "/admin/media/add", "HTTP_FORBIDDEN"],
            '/admin/media/add as frozen user' => ["userfrozen@zaoui.com", "/admin/media/add", "HTTP_FORBIDDEN"],

            '/admin/media/delete/id as admin user' => ["ina@zaoui.com", "/admin/media/delete/id", "HTTP_FOUND"],
            '/admin/media/delete/1 as standard user' => ["userlambda@zaoui.com", "/admin/media/delete/1", "HTTP_FORBIDDEN"],
            '/admin/media/delete/1 as frozen user' => ["userfrozen@zaoui.com", "/admin/media/delete/1", "HTTP_FORBIDDEN"],


            '/admin/guests as admin user' => ["ina@zaoui.com", "/admin/guests", "HTTP_OK"],
            '/admin/guests as standard user' => ["userlambda@zaoui.com", "/admin/guests", "HTTP_FORBIDDEN"],
            '/admin/guests as frozen user' => ["userfrozen@zaoui.com", "/admin/guests", "HTTP_FORBIDDEN"],

            '/admin/guest/add as admin user' => ["ina@zaoui.com", "/admin/guest/add", "HTTP_OK"],
            '/admin/guest/add as standard user' => ["userlambda@zaoui.com", "/admin/guest/add", "HTTP_FORBIDDEN"],
            '/admin/guest/add as frozen user' => ["userfrozen@zaoui.com", "/admin/guest/add", "HTTP_FORBIDDEN"],

            '/admin/guest/update/id as standard user' => ["ina@zaoui.com", "/admin/guest/update/id", "HTTP_OK"],
            '/admin/guest/update/1 as standard user' => ["userlambda@zaoui.com", "/admin/guest/update/1", "HTTP_FORBIDDEN"],
            '/admin/guest/update/1 as frozen user' => ["userfrozen@zaoui.com", "/admin/guest/update/1", "HTTP_FORBIDDEN"],

            '/admin/guest/delete/id as standard user' => ["ina@zaoui.com", "/admin/guest/delete/id", "HTTP_FOUND"],
            '/admin/guest/delete/1 as standard user' => ["userlambda@zaoui.com", "/admin/guest/delete/1", "HTTP_FORBIDDEN"],
            '/admin/guest/delete/1 as frozen user' =>  ["userfrozen@zaoui.com", "/admin/guest/delete/1", "HTTP_FORBIDDEN"],


            '/guest/media as admin user' => ["ina@zaoui.com", "/guest/media", "HTTP_OK"],
            '/guest/media as standard user' => ["userlambda@zaoui.com", "/guest/media", "HTTP_OK"],
            '/guest/media as frozen user' => ["userfrozen@zaoui.com", "/guest/media", "HTTP_FORBIDDEN"],

            '/guest/media/add as admin user' => ["ina@zaoui.com", "/guest/media/add", "HTTP_OK"],
            '/guest/media/add as standard user' => ["userlambda@zaoui.com", "/guest/media/add", "HTTP_OK"],
            '/guest/media/add as frozen user' => ["userfrozen@zaoui.com", "/guest/media/add", "HTTP_FORBIDDEN"],

            '/guest/media/delete/id as admin user' => ["ina@zaoui.com", "/guest/media/delete/id", "HTTP_FOUND"],
            '/guest/media/delete/idbis as standard user' => ["userlambda@zaoui.com", "/guest/media/delete/idbis", "HTTP_FOUND"],
            '/guest/media/delete/1 as frozen user' => ["userfrozen@zaoui.com", "/guest/media/delete/1", "HTTP_FORBIDDEN"],
        ];
    } 

    public function testAddAlbumViaPostRequestByUnconnectedUser(): void
    {
        $postAlbum = "Post Album";
        $crawler = $this->client->request('POST', '/admin/album/add', [
            'album' => [
                'name' => $postAlbum,
            ]
        ]);
        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
        $crawler = $this->client->followRedirect();
        $this->assertSelectorExists('form input[name="_username"]');

        $album = static::getContainer()->get(\Doctrine\ORM\EntityManagerInterface::class)
            ->getRepository(\App\Entity\Album::class)
            ->findOneBy(['name' => $postAlbum]);

        $this->assertNull($album);

    }

    public function testAddAlbumViaPostRequestByConnectedUser(): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'userlambda@zaoui.com']);
        $this->client->loginUser($user);

        $postAlbum = "Post Album";
    
        $this->client->request('POST', '/admin/album/add', [
            'album' => [
                'name' => $postAlbum,
            ]
        ]);
    
        $this->assertResponseStatusCodeSame(403);
    
        $album = static::getContainer()->get(\Doctrine\ORM\EntityManagerInterface::class)
            ->getRepository(\App\Entity\Album::class)
            ->findOneBy(['name' => $postAlbum]);
    
        $this->assertNull($album);
    }
    
}
