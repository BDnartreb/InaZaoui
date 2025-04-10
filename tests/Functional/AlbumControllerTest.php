<?php

namespace App\Tests\Functional;

use App\Entity\Album;
use App\Entity\User;
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

    // }

    // public function testDeleteAlbum(): void
    // {

    // }
}
