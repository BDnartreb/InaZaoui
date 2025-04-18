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

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->em = $container->get('doctrine')->getManager();
    }

    public function testDisplayAlbumIndexPage(): void
    {
        $userRepository = $this->em->getRepository(User::class);
        $admin = $userRepository->findOneBy(['email' => 'ina@zaoui.com']);

        $this->client->loginUser($admin);

        $this->client->request('GET', '/admin/album');

        // $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        //$this->assertSelectorTextContains('h1', 'Albums');
    }

    public function testAddAlbum(): void
    {
        // Given : /admin/album/add
        // When : Enter new album name and clic on add button
        // Then : the album is added to the database 
        // And : it displays /admin/album with the name modified

        $userRepository = $this->em->getRepository(User::class);
        $admin = $userRepository->findOneBy(['email' => 'ina@zaoui.com']);

        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', '/admin/album/add');
        
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form([
            'album[name]' => 'Test Album',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/album');

        $this->client->followRedirect();

        $albumRepository = $this->em->getRepository(Album::class);
        $newAlbum = $albumRepository->findOneBy(['name' => 'Test Album']);
     
        $this->assertNotNull($newAlbum);
        $this->assertEquals('Test Album', $newAlbum->getName());
    }

    // public function testUpdateAlbum(): void
    // {
            // Given : /admin/album/update/{id} of the album
        // When : Modify its name and clic on modify button
        // Then : the name is changed 
        // And : Display /admin/album with the name modified

    // }

    // public function testDeleteAlbum(): void
    // {
        // Given : /admin/album/delete/{id} of the album or /admin/album
        // When : Push Enter button or Clic on delete button
        // Then : The album is deleted but not the associated medias
        // And : Display /admin/album without the removed album
    // }

    // public function testAddAlbumViaPostRequest(){
    //}

    // public function testUpdateAlbumViaPushRequest(){
    //}

    // public function testDeleteAlbumViaDeleteRequest(){
    //}
}
