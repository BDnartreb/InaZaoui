<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Album;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class AlbumControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    private string $albumName = 'Album Test';
    private string $albumModified = 'Album Test Modified';

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->em = $container->get('doctrine')->getManager();
    }
 
    public function testAddAlbum(): void
    {
        // Given : /admin/album/add
        // When : Enter new album name and clic on add button
        // Then : the album is added to the database 
        // And : it displays /admin/album with the name modified

        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
        $this->client->loginUser($admin);
        $crawler = $this->client->request('GET', '/admin/album/add');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form([
            'album[name]' => $this->albumName,
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin/album');
        $this->client->followRedirect();
        $newAlbum = $this->em->getRepository(Album::class)->findOneBy(['name' => $this->albumName]);
        $this->assertNotNull($newAlbum);
        $this->assertEquals($this->albumName, $newAlbum->getName());
    }

    public function testUpdateAlbum(): void
    {
            // Given : /admin/album/update/{id} of the album
        // When : Modify its name and clic on modify button
        // Then : the name is changed 
        // And : Display /admin/album with the name modified

        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
        $this->client->loginUser($admin);
        $album = $this->em->getRepository(Album::class)->findOneBy(['name' => $this->albumName]);
        $albumId = $album->getId();
        $crawler = $this->client->request('GET', '/admin/album/update/' . $albumId);
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Modifier')->form([
            'album[name]' => $this->albumModified,
        ]);
        $this->client->submit($form);
        $albumUpdated = $this->em->getRepository(Album::class)->findOneBy(['name' => $this->albumModified]);
        $this->assertEquals($this->albumModified, $albumUpdated->getName());

            // // Invalid modification in the database
            // $albumModifiedId = $albumModified->getId();
            // $crawler = $this->client->request('GET', '/admin/album/update/' . $albumModifiedId);
            // $form = $crawler->selectButton('Modifier')->form([
            //     'album[name]' => 'Album 1',
            // ]);
            // $this->client->submit($form);

        $this->assertResponseRedirects('/admin/album');
        $this->client->followRedirect();
    }

    public function testDeleteAlbum(): void
    {
        // Given : /admin/album/delete/{id} of the album or /admin/album
        // When : Push Enter button or Clic on delete button
        // Then : The album is deleted but not the associated medias
        // And : Display /admin/album without the removed album

        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
        $this->client->loginUser($admin);

        $album = $this->em->getRepository(Album::class)->findOneBy(['name' => $this->albumModified]);
        $albumId = $album->getId();
        $crawler = $this->client->request('GET', '/admin/album/delete/' . $albumId);

        $albumDeleted = $this->em->getRepository(Album::class)->findOneBy(['name' => $this->albumModified]);
        $this->assertEquals($albumDeleted, null);

        $this->assertResponseRedirects('/admin/album');
        $this->client->followRedirect();
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
