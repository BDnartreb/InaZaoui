<?php

namespace App\Tests\Functional;

use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GuestMediaControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private KernelBrowser $client;
    private User $user;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->em = $container->get('doctrine')->getManager();
        $this->user = $this->em->getRepository(User::class)->findOneBy(['email' => 'userdeletemedias@zaoui.com']);
        $this->client->loginUser($this->user);
    }

    public function testDeleteMedia(): void
    {
        $newMediaTitle = 'Titre userDeleteMedias 1';
        $media = $this->em->getRepository(Media::class)->findOneBy(['title' => $newMediaTitle]);
        $mediaId = $media->getId();
        $crawler = $this->client->request('GET', '/guest/media/delete/' . $mediaId);

        $deletedMedia = $this->em->getRepository(User::class)->find($mediaId);
        $this->assertEquals($deletedMedia, null);

        $this->assertResponseRedirects('/guest/media');
        $this->client->followRedirect();
    }
}
