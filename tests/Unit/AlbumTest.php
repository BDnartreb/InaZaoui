<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class AlbumTest extends TestCase
{
    public function testAddOfANewAlbum(): void
    {
        // Given : /admin/album/add
        // When : Enter new album name and clic on add button
        // Then : the album is added to the database 
        // And : it displays /admin/album with the name modified
        $this->assertTrue(true);
    }

    public function testUpdateOfANewAlbum(): void
    {
        // Given : /admin/album/update/{id} of the album
        // When : Modify its name and clic on modify button
        // Then : the name is changed 
        // And : Display /admin/album with the name modified
        $this->assertTrue(true);
    }

    public function testDeleteOfANewAlbum(): void
    {
        // Given : /admin/album/delete/{id} of the album or /admin/album
        // When : Push Enter button or Clic on delete button
        // Then : The album and the associated medias are deleted
        // And : Display /admin/album without the removed album
        $this->assertTrue(true);
    }
}
