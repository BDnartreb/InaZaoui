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

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->em = $container->get('doctrine')->getManager();
    }

    public function testAddGuest(): void
    {
        $admin = $this->em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', '/admin/guest/add');
        $this->assertResponseIsSuccessful();
        //dd($crawler);

        $newGuestEmail = "newguest@zaoui.com";
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
        $this->assertTrue($this->client->getResponse()->isRedirection());
        // $location = $this->client->getResponse()->headers->get('Location');
        // dump($location); // ou echo $location;
                // $this->assertResponseRedirects('/guests');
        // $this->client->followRedirect();
    }

  

    // public function testUpdateGuest(): void
    // {
    //     $client = static::createClient();
    //     $client->request('GET', '/guest');

    //     self::assertResponseIsSuccessful();
    // }

    // public function testDeleteGuest(): void
    // {
    //     // check medias are deleted too
    //     $client = static::createClient();
    //     $client->request('GET', '/guest');

    //     self::assertResponseIsSuccessful();
    // }

    // public function testAddGuestViaPostRequest(){
    //}

    // public function testUpdateGuestViaPushRequest(){
    //}

    // public function testDeleteGuestViaDeleteRequest(){
    //}


    // public function testShouldDisplayOneGuestPage($guestEmail, $guestLastName): void
    // {
    //     $guestId = $this->em->getRepository(User::class)->findOneBy(['email' => $guestEmail])->getId();
    //     $crawler = $this->client->request('GET', '/guest/' . $guestId);
    //     $this->assertResponseIsSuccessful();
    //     $this->assertSelectorTextContains('h3', $guestLastName);
    // }
    
}
