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

    /**
     * @dataProvider provideMediaData
     */
    public function testDeleteMedia(?string $MediaTitleBefore, ?string $MediaTitleAfter): void
    {
        $media = $this->em->getRepository(Media::class)->findOneBy(['title' => $MediaTitleBefore]);
        $mediaId = $media->getId();
        $crawler = $this->client->request('GET', '/guest/media/delete/' . $mediaId);

        $deletedMedia = $this->em->getRepository(Media::class)->find($mediaId)->getTitle();
        $this->assertEquals($deletedMedia, $MediaTitleAfter);

        $this->assertResponseRedirects('/guest/media');
        $this->client->followRedirect();
    }

    /**
    * @return array<array{string, ?string}>
    */
    public function provideMediaData(): array
    {
        return [
            ['Titre userLambda 9', null],
            ['Titre albumDeleteMedia20', 'Titre albumDeleteMedia20'],
        ];
    }
}
