<?php

namespace App\Tests\Functional;

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
            ["ina@zaoui.com", "/admin/album", "HTTP_OK"],
            ["user0@zaoui.com", "/admin/album", "HTTP_FORBIDDEN"],
            ["userfrozen@zaoui.com", "/admin/album", "HTTP_FORBIDDEN"],

            //["ina@zaoui.com", "/admin/album/add", "HTTP_OK"],
            ["user0@zaoui.com", "/admin/album/add", "HTTP_FORBIDDEN"],
            ["userfrozen@zaoui.com", "/admin/album/add", "HTTP_FORBIDDEN"],

            ["ina@zaoui.com", "/admin/media", "HTTP_OK"],
            ["user0@zaoui.com", "/admin/media", "HTTP_FORBIDDEN"],
            ["userfrozen@zaoui.com", "/admin/media", "HTTP_FORBIDDEN"],

            //["ina@zaoui.com", "/admin/media/add", "HTTP_OK"],
            ["user0@zaoui.com", "/admin/media/add", "HTTP_FORBIDDEN"],
            ["userfrozen@zaoui.com", "/admin/media/add", "HTTP_FORBIDDEN"],

            ["ina@zaoui.com", "/admin/guests", "HTTP_OK"],
            ["user0@zaoui.com", "/admin/guests", "HTTP_FORBIDDEN"],
            ["userfrozen@zaoui.com", "/admin/guests", "HTTP_FORBIDDEN"],

            //["ina@zaoui.com", "/admin/guest/add", "HTTP_OK"],
            ["user0@zaoui.com", "/admin/guest/add", "HTTP_FORBIDDEN"],
            ["userfrozen@zaoui.com", "/admin/guest/add", "HTTP_FORBIDDEN"]

            // ["ina@zaoui.com", "/admin/album/update/1", "HTTP_OK"],
            // ["ina@zaoui.com", "/admin/album/delete/1", "HTTP_OK"],

            // ["ina@zaoui.com", "/admin/media/delete/1", "HTTP_OK"],
,
            // ["ina@zaoui.com", "/admin/guest/update/1", "HTTP_OK"],
            // ["ina@zaoui.com", "/admin/guest/delete/1", "HTTP_OK"],
    
            // ["user0@zaoui.com", "/admin/album/update/1", "HTTP_FORBIDDEN"],
            // ["user0@zaoui.com", "/admin/album/delete/1", "HTTP_FORBIDDEN"],
            
            // ["user@zaoui.com", "/admin/media/delete/1", "HTTP_FORBIDDENN"],
          
            // ["user0@zaoui.com", "/admin/guest/update/", "HTTP_FORBIDDEN"],
            // ["user0@zaoui.com", "/admin/guest/delete/", "HTTP_FORBIDDEN"],
                   
            // ["userfrozen@zaoui.com", "/admin/album/update/1", "HTTP_FORBIDDEN"],
            // ["userfrozen@zaoui.com", "/admin/album/delete/1", "HTTP_FORBIDDEN"],
           
            // ["userfrozen@zaoui.com", "/admin/media/delete/1", "HTTP_FORBIDDEN"],
          
            // ["userfrozen0@zaoui.com", "/admin/guest/update/", "HTTP_FORBIDDEN"],
            // ["userfrozen0@zaoui.com", "/admin/guest/delete/", "HTTP_FORBIDDEN"],
        ];
    } 

    public function testAddAlbumWithPostByUnconnectedUser(): void
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

    public function testAddAlbumWithPostByConnectedUser(): void
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'user0@zaoui.com']);
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
