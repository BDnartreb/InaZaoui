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

    public function testAddMedia(): void
        {
            $crawler = $this->client->request('GET', '/admin/media/add');
            //file_put_contents('form-debug.html', $crawler->html());
            $this->assertResponseIsSuccessful();
            
            $userId = $this->em->getRepository(User::class)->findOneBy(['email' => "user0@zaoui.com"])->getId(); 

            $media = new Media();
            $mediaTitle = 'imageTest.jpg';
            $media->setTitle($mediaTitle);
                    
            //$tempPath = sys_get_temp_dir() . '/imageTest.jpg';
            // file_put_contents($tempPath, str_repeat('0', 1 * 1024 * 1024));
            $tempPath =   __DIR__.'/../Unit/img-inf_2Mo.JPG';
            $this->assertFileExists($tempPath);

           $file = new UploadedFile(
                $tempPath,
                'imageTest.jpg',
                'image/jpeg',
                null,
                true
            );

            $form = $crawler->selectButton('Ajouter')->form([
                'media[user]' => $userId,
                'media[album]' => "",
                'media[title]' => $mediaTitle,
                'media[file]' => $file,
            ]);

            $this->client->submit($form);
            //$this->assertResponseStatusCodeSame(302);
   
            $newMedia = $this->em->getRepository(Media::class)->findOneBy(['title' => $mediaTitle]);
            //$newMedia = $this->em->getRepository(Media::class)->findOneBy(['path' => $tempPath]);
            //dd($newMedia);
            
            $this->assertNotNull($newMedia);
            $this->assertEquals($mediaTitle, $newMedia->getTitle());
            //$this->assertTrue($this->client->getResponse()->isRedirection());
            $this->assertResponseRedirects('/admin/media');
            $this->client->followRedirect();
        }

    public function testDeleteMedia(): void
        {
            $newMediaTitle = 'Titre userLambda 2';
            $media = $this->em->getRepository(Media::class)->findOneBy(['title' => $newMediaTitle]);
            $mediaId = $media->getId();
            $crawler = $this->client->request('GET', '/admin/media/delete/' . $mediaId);
    
            $deletedMedia = $this->em->getRepository(Media::class)->find($mediaId);
            $this->assertEquals($deletedMedia, null);
    
            $this->assertResponseRedirects('/admin/media');
            $this->client->followRedirect();
        }
}


