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
        $this->user = $this->em->getRepository(User::class)->findOneBy(['email' => 'userlambda@zaoui.com']);
        $this->client->loginUser($this->user);
    }

    public function testDisplayGuestMediaPage(): void
    {
        $crawler = $this->client->request('GET', '/guest/media');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('th', "Image");
    }

    public function testGuestDeleteHisMedia(): void
    {
        $MediaTitle = 'Titre userLambda 9';

        $media = $this->em->getRepository(Media::class)->findOneBy(['title' => $MediaTitle]);
        $mediaId = $media->getId();
        $crawler = $this->client->request('GET', '/guest/media/delete/' . $mediaId);

        $deletedMedia = $this->em->getRepository(Media::class)->find($mediaId);
        $this->assertNull($deletedMedia);

        $this->assertResponseRedirects('/guest/media');
        $this->client->followRedirect();
    }

    public function testGuestDeleteNotHisMedia(): void
    {
        $MediaTitle = 'Titre albumDeleteMedia20';
        $media = $this->em->getRepository(Media::class)->findOneBy(['title' => $MediaTitle]);
        $mediaId = $media->getId();
        $crawler = $this->client->request('GET', '/guest/media/delete/' . $mediaId);

        $deletedMedia = $this->em->getRepository(Media::class)->find($mediaId)->getTitle();
        $this->assertEquals($deletedMedia, $MediaTitle);

        $this->assertResponseRedirects('/guest/media');
        $this->client->followRedirect();
    }

}
