<?php

namespace App\Tests\Functional;

use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaControllerTest extends WebTestCase
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

    public function testDeleteMedia(): void
    {
        $newMediaTitle = 'Titre userLambda 2';
        $media = $this->em->getRepository(Media::class)->findOneBy(['title' => $newMediaTitle]);
        $mediaId = $media->getId();
        $crawler = $this->client->request('GET', '/admin/media/delete/' . $mediaId);

        $deletedMedia = $this->em->getRepository(User::class)->find($mediaId);
        $this->assertEquals($deletedMedia, null);

        $this->assertResponseRedirects('/admin/media');
        $this->client->followRedirect();
    }

    // public function testAddMedia(): void
    // {
    //     $crawler = $this->client->request('GET', '/admin/media/add');
    //     $this->assertResponseIsSuccessful();
    //     //dd($crawler);
    //     $user = $this->em->getRepository(User::class)->findOneBy(['email' => "user0@zaoui.com"])->getId();
        
    //     $filePath = __DIR__.'/public/images/img-inf_2Mo.JPG';
    //     $uploadedFile = new UploadedFile(
    //         $filePath,
    //         'img-sup_2Mo.jpg',
    //         mime_content_type($filePath),
    //         null,
    //         true // important: force le test (true = fichier déjà déplacé)
    //     );

    //     $form = $crawler->selectButton('Ajouter')->form([
    //         'media[user]' => $user,
    //         'media[album]' => "",
    //         'media[title]' => $this->newMediaTitle,
    //         'media[file]' => $uploadedFile,
    //     ]);
    //     $this->client->submit($form);

    //     $newMedia = $this->em->getRepository(Media::class)->findOneBy(['title' => $this->newMediaTitle]);
    //     $this->assertNotNull($newMedia);
    //     $this->assertEquals($this->newMediaTitle, $newMedia->getTitle());
    //     //$this->assertTrue($this->client->getResponse()->isRedirection());
    //     $this->assertResponseRedirects('/admin/medias');
    //     $this->client->followRedirect();
    // }


}
