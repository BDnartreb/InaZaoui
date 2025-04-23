<?php

namespace App\Tests\Functional;

use App\Entity\Album;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Constraint\GreaterThan;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function PHPUnit\Framework\greaterThan;

class HomeControllerTest extends WebTestCase
{
    private KernelBrowser $client;

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
    public function testShouldDisplayOneGuestPage(string $guestEmail, string $guestLastName): void
    {
        $container = static::getContainer();
        $this->em = $container->get('doctrine')->getManager();
        $guest = $this->em->getRepository(User::class)->findOneBy(['email' => $guestEmail]);
        $guestId = $guest->getId();
        $crawler = $this->client->request('GET', '/guest/' . $guestId);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', $guestLastName);
            self::assertThat(
        $crawler->filter('img')->count(),
        new GreaterThan(1)
    );
    }

    /**
    * @return array<array{string, string}>
    */
    public static function provideOneGuest(): array
    {
        return [
            ["userlambda@zaoui.com", "userlambdaLastName"],
        ];
    }

    /**
    * 
    * @dataProvider provideFrozenGuest
    */
    public function testShouldNotDisplayGuestPageToRoleFrozen(string $guestEmail): void
    {
        $container = static::getContainer();
        $this->em = $container->get('doctrine')->getManager();
        $guestId = $this->em->getRepository(User::class)->findOneBy(['email' => $guestEmail])->getId();
        $crawler = $this->client->request('GET', '/guest/' . $guestId);
        $this->assertResponseRedirects('/guests');
        $this->client->followRedirect();
    }
    
    /**
     * @return array<array{string}>
     */
    public static function provideFrozenGuest(): array
    {
        return [
            ["userfrozen@zaoui.com"],
        ];
    }

    public function testShouldNotDisplayFrozenMediasInPortfolio(): void
    {
        $container = static::getContainer();
        $this->em = $container->get('doctrine')->getManager();
        $albumId = $this->em->getRepository(Album::class)->findOneBy(['name' => 'Album Frozen'])->getId();
        $crawler = $this->client->request('GET', '/portfolio/' . $albumId);
        $this->assertResponseIsSuccessful();
        self::assertSelectorCount(0, 'col-4 media mb-4');

        $crawler = $this->client->request('GET', '/portfolio');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', "Portfolio");
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
