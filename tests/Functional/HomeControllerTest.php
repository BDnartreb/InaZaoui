<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    private $client;

    private EntityManagerInterface $em;

    public function setUp(): void
    {
        $this->client = static::createClient(); 
    }

    public function testShouldDisplayHomePage(): void
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Photographe');
    }

    public function testShouldDisplayGuestsPage(): void
    {
        $crawler = $this->client->request('GET', '/guests');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Invités');
    }

    /**
     * 
     * @dataProvider provideOneGuest
     */
    public function testShouldDisplayOneGuestPage($guestEmail, $guestLastName): void
    {
        $container = static::getContainer();
        $this->em = $container->get('doctrine')->getManager();
        $guestId = $this->em->getRepository(User::class)->findOneBy(['email' => $guestEmail])->getId();
        $crawler = $this->client->request('GET', '/guest/' . $guestId);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', $guestLastName);
    }

    public static function provideOneGuest()
    {
        return [
            ["userfrozen@zaoui.com", "UserFrozen"],
        ];
    }

    public function testShouldDisplayPortfolioPage(): void
    {
        $crawler = $this->client->request('GET', '/portfolio');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', "Portfolio");
    }

    public function testShouldDisplayPortfolioAlbum1(): void
    {
        $crawler = $this->client->request('GET', '/portfolio/1');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', "Portfolio");
    }


    public function testShouldDisplayAboutPage(): void
    {
        $crawler = $this->client->request('GET', '/about');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', "Qui suis-je ?");
    }
}
