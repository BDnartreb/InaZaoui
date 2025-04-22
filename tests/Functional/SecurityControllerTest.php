<?php

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityControllerTest extends WebTestCase
{
    protected EntityManagerInterface $em;
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->em = $container->get('doctrine')->getManager();
    }

    public function testThatLoginShouldSucceeded(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'user0@zaoui.com',
            '_password' => 'password',
        ]);

        $this->client->submit($form);

        $authorizationChecker = self::getContainer()->get(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED'));
        $crawler = $this->client->request('GET', '/logout');
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
    }

    /**
    * 
    * @dataProvider provideBadUser
    */
    public function testThatLoginShouldFailed(string $email, string $password): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => $email,
            '_password' => $password,
        ]);
        
        $this->client->submit($form);
        $authorizationChecker = self::getContainer()->get(AuthorizationCheckerInterface::class);
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
    }

    /**
    * @return array<array{string, string}>
    */
    public function provideBadUser(){
        return [            
            ["baduser@zaoui.com", "password"],
            ["ina@zaoui.com", "badpassword"],
        ];


    }

}
