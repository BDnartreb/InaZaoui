<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GuestControllerTest extends WebTestCase
{
    public function testAddGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/guest');

        self::assertResponseIsSuccessful();
    }

    public function testUpdateGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/guest');

        self::assertResponseIsSuccessful();
    }

    public function testDeleteGuest(): void
    {
        // check medias are deleted too
        $client = static::createClient();
        $client->request('GET', '/guest');

        self::assertResponseIsSuccessful();
    }

    // public function testAddGuestViaPostRequest(){
    //}

    // public function testUpdateGuestViaPushRequest(){
    //}

    // public function testDeleteGuestViaDeleteRequest(){
    //}
}
