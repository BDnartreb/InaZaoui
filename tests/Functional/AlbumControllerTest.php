<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Album;
use App\Entity\Media;
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
        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
        $this->client->loginUser($admin);
    }
 
    public function testDisplayAdminAlbumPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/album');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('td', "Album 1");
    }

    public function testAdminAlbumAdd(): void
    {
        $albumName = 'Album Test';
        $crawler = $this->client->request('GET', '/admin/album/add');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form([
            'album[name]' => $albumName,
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin/album');
        $this->client->followRedirect();
        $newAlbum = $this->em->getRepository(Album::class)->findOneBy(['name' => $albumName]);
        $this->assertNotNull($newAlbum);
        $this->assertEquals($albumName, $newAlbum->getName());
    }

    public function testAdminAlbumUpdate(): void
    {
        $albumName = 'Album 3';
        $albumModified = 'Album 3 Modifié';
        $album = $this->em->getRepository(Album::class)->findOneBy(['name' => $albumName]);
        $albumId = $album->getId();
        $crawler = $this->client->request('GET', '/admin/album/update/' . $albumId);
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Modifier')->form([
            'album[name]' => $albumModified,
        ]);
        $this->client->submit($form);
        $albumUpdated = $this->em->getRepository(Album::class)->findOneBy(['name' => $albumModified]);
        $this->assertEquals($albumModified, $albumUpdated->getName());

        $this->assertResponseRedirects('/admin/album');
        $this->client->followRedirect();
    }

    public function testAdminAlbumDelete(): void
    {
        $albumName = 'Album Deleted';
        $mediaName = 'Titre albumDeleteMedia20';

        $album = $this->em->getRepository(Album::class)->findOneBy(['name' => $albumName]);
        $albumId = $album->getId();
        $crawler = $this->client->request('GET', '/admin/album/delete/' . $albumId);

        $albumDeleted = $this->em->getRepository(Album::class)->findOneBy(['name' => $albumName]);
        $mediaAlbumDeleted = $this->em->getRepository(Media::class)->findOneBy(['title' => $mediaName]);
        $this->assertEquals($albumDeleted, null);
        $this->assertEquals($mediaName, $mediaAlbumDeleted->getTitle());

        $this->assertResponseRedirects('/admin/album');
        $this->client->followRedirect();
    }

    /**
     * @dataProvider provideRenderData
     */
    public function testAdminAlbumRender(string $route, string $buttonName): void
    {
        $crawler = $this->client->request('GET', $route);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input');
        $this->assertSelectorTextContains('button', $buttonName);
    }

    /**
    * @return array<array{string, string}>
    */
    public function provideRenderData(): array
    {
        return [
            ['/admin/album/add', 'Ajouter'],
            ['/admin/album/update/1', 'Modifier'],
        ];
    }

}
