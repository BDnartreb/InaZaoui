<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{

    private $client;

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
    
    public function testShouldDisplayOneGuestPage($guestId, $guestName): void
    {
        $crawler = $this->client->request('GET', '/guest/' . $guestId);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', $guestName);
    }

    public static function provideOneGuest()
    {
        return [
            [1, "Ina"],
        ];
    }

    /**
    * 
    * @dataProvider provideAlbum
    */
    public function testShouldDisplayPortfolioPage($albumId, $albumName): void
    {
        $crawler = $this->client->request('GET', '/portfolio/' . $albumId);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', "Portfolio");
    }

    public static function provideAlbum()
    {
        return [
            [1, "Album 1"],
        ];
    }

    public function testShouldDisplayAboutPage(): void
    {
        $crawler = $this->client->request('GET', '/about');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', "Qui suis-je ?");
    }
}
