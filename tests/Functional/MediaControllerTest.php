<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MediaTest extends WebTestCase
{
    public function testUploadMedia(): void
    {
        // Given : /admin/media/add
        // When : Fill the form and clic on add button
        // Then : the media is added to the database 
        // And : it displays /admin/media with the new media
        $this->assertTrue(true);
    }

    public function testDeleteMedia(): void
    {
        // Given : /admin/media/delete/{id} of the album or /admin/media
        // When : Push Enter button or Clic on delete button
        // Then : The media is deleted
        // And : Display /admin/media without the removed media
        $this->assertTrue(true);
    }

    // public function testAddMediaViaPostRequest(){
    //}

    // public function testUpdateMediaViaPushRequest(){
    //}

    // public function testDeleteMediaViaDeleteRequest(){
    //}
}
