<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GuestControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    private $newGuestEmail = "newguest@zaoui.com";
    private $modifiedGuestFirstName = "modifiedFirstName";

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->em = $container->get('doctrine')->getManager();
        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
        $this->client->loginUser($admin);
    }

    public function testAddGuest(): void
    {
        $crawler = $this->client->request('GET', '/admin/guest/add');
        $this->assertResponseIsSuccessful();
        
        $form = $crawler->selectButton('Ajouter')->form([
            'user[email]' => $this->newGuestEmail,
            'user[firstname]' => 'newguestFirstName',
            'user[lastname]' => 'newguestLastName',
            'user[description]' => 'newguestDescription',
        ]);
        $this->client->submit($form);

        $newGuest = $this->em->getRepository(User::class)->findOneBy(['email' => $this->newGuestEmail]);
        $this->assertNotNull($newGuest);
        $this->assertEquals($this->newGuestEmail, $newGuest->getEmail());
        //$this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertResponseRedirects('/admin/guests');
        $this->client->followRedirect();
    }

    public function testUpdateGuest(): void
    {
        $newGuest = $this->em->getRepository(User::class)->findOneBy(['email' => $this->newGuestEmail]);
        $guestId = $newGuest->getId();
        $crawler = $this->client->request('GET', '/admin/guest/update/' . $guestId);
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'user_update[firstName]' => $this->modifiedGuestFirstName,
            'user_update[password]' => 'password',
            'user_update[email]' => $newGuest->getEmail(),
            'user_update[roles]' => $newGuest->getRoles(),
            'user_update[lastName]' => $newGuest->getLastName(),

        ]);
        $this->client->submit($form);
        $updatedGuest = $this->em->getRepository(User::class)->find($guestId);
        $this->assertEquals($this->modifiedGuestFirstName, $updatedGuest->getFirstName());

        $this->assertResponseRedirects('/admin/guests');
        $this->client->followRedirect();
    }

    public function testDeleteGuest(): void
    {
        $newGuest = $this->em->getRepository(User::class)->findOneBy(['email' => $this->newGuestEmail]);
        $guestId = $newGuest->getId();
        $crawler = $this->client->request('GET', '/admin/guest/delete/' . $guestId);

        $deletedGuest = $this->em->getRepository(User::class)->find($guestId);
        $this->assertEquals($deletedGuest, null);

        $this->assertResponseRedirects('/admin/guests');
        $this->client->followRedirect();
    }

//     public function testDeletingUserAlsoDeletesMedias(): void
// {
//     $repo = $this->em->getRepository(User::class);
//     $user = $repo->findOneBy(['email' => 'userdeletemedias@zaoui.com']);

//     $mediaRepo = $this->em->getRepository(Media::class);
//     $mediaCountBefore = $mediaRepo->count(['user' => $user]);

//     $this->assertGreaterThan(0, $mediaCountBefore);

//     $this->em->remove($user);
//     $this->em->flush();

//     $mediaCountAfter = $mediaRepo->count(['user' => $user]);
//     $this->assertEquals(0, $mediaCountAfter);
// }

    
}
