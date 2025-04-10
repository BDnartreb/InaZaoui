<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class AlbumControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private $client;
    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testDisplayAlbumIndexPage(): void
    {
        $container = $this->client->getContainer();
        $em = $container->get('doctrine')->getManager();
        $userRepository = $em->getRepository(User::class);
        $admin = $userRepository->findOneBy(['email' => 'ina@zaoui.com']);

        $this->client->loginUser($admin);

        $this->client->request('GET', '/admin/album');

        // $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        //$this->assertSelectorTextContains('h1', 'Albums');
    }

    public function testDisplayAlbumAddForm(): void
    {

    }

    public function testDisplayAlbumUpdateForm(): void
    {

    }
}
