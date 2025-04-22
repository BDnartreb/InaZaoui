<?php

namespace App\Tests\Functional;

use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GuestControllerTest extends WebTestCase
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

    public function testDisplayAdminGuestsPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/guests');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('th', "Nom");
    }

    public function testAddGuest(): void
    {
        $newGuestEmail = "newguest@zaoui.com";

        $crawler = $this->client->request('GET', '/admin/guest/add');
        $this->assertResponseIsSuccessful();
        
        $form = $crawler->selectButton('Ajouter')->form([
            'user[email]' => $newGuestEmail,
            'user[firstname]' => 'newguestFirstName',
            'user[lastname]' => 'newguestLastName',
            'user[description]' => 'newguestDescription',
        ]);
        $this->client->submit($form);

        $newGuest = $this->em->getRepository(User::class)->findOneBy(['email' => $newGuestEmail]);
        $this->assertNotNull($newGuest);
        $this->assertEquals($newGuestEmail, $newGuest->getEmail());
        //$this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertResponseRedirects('/admin/guests');
        $this->client->followRedirect();
    }

    public function testUpdateGuest(): void
    {
        $guestEmail = "user0@zaoui.com";
        $modifiedGuestFirstName = "user0 modified";
        $newGuest = $this->em->getRepository(User::class)->findOneBy(['email' => $guestEmail]);
        $guestId = $newGuest->getId();
        $crawler = $this->client->request('GET', '/admin/guest/update/' . $guestId);
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'user_update[firstName]' => $modifiedGuestFirstName,
            'user_update[password]' => 'password',
            'user_update[email]' => $newGuest->getEmail(),
            'user_update[roles]' => $newGuest->getRoles(),
            'user_update[lastName]' => $newGuest->getLastName(),

        ]);
        $this->client->submit($form);
        $updatedGuest = $this->em->getRepository(User::class)->find($guestId);
        $this->assertEquals($modifiedGuestFirstName, $updatedGuest->getFirstName());

        $this->assertResponseRedirects('/admin/guests');
        $this->client->followRedirect();
    }

    public function testDeleteGuest(): void
    {
        $userDeletedEmail = "userdeleted@zaoui.com";
        $guestDeleted = $this->em->getRepository(User::class)->findOneBy(['email' => $userDeletedEmail]);

        $mediasGuestDeleted = $this->em->getRepository(Media::class);
        $mediaCountBeforeDelete = $mediasGuestDeleted->count(['user' => $guestDeleted]);
        $this->assertGreaterThan(0, $mediaCountBeforeDelete);

        $guestId = $guestDeleted->getId();
        $crawler = $this->client->request('GET', '/admin/guest/delete/' . $guestId);

        $deletedGuest = $this->em->getRepository(User::class)->find($guestId);
        $this->assertEquals($deletedGuest, null);

        $mediaCountAfter = $mediasGuestDeleted->count(['user' => $guestDeleted]);
        $this->assertEquals(0, $mediaCountAfter);

        $this->assertResponseRedirects('/admin/guests');
        $this->client->followRedirect();
    }
  
     /**
     * @dataProvider provideRenderData
     */
    public function testAdminGuestRender(string $route, string $buttonName): void
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
            ['/admin/guest/add', 'Ajouter'],
            ['/admin/guest/update/1', 'Modifier'],
        ];
    }


}
