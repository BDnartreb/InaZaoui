<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Form\MediaType;
//use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\PreloadedExtension;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


//class MediaTypeTest extends TypeTestCase
class MediaTypeTest extends TestCase
{
    private UploadedFile $validFile;

    protected function setUp(): void
    {
        $path = sys_get_temp_dir().'/test_image.jpg';
        copy(__DIR__.'/../../public/images/test.jpg', $path);

        $this->validFile = new UploadedFile(
            $path,
            'test.jpg',
            'image/jpeg',
            null,
            true
        );

        parent::setUp();
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'title' => 'TitreMediaTypeTest',
        ];

        $media = new Media();

        $user = new User();
        $user->setLastName('UserLastName0');

        $album = new Album();
        $album->setName('Album 1');

        $form = $this->createForm(MediaType::class, $media, [
            'csrf_protection' => false, // pour éviter les erreurs CSRF en test unitaire
        ]);

        $form->submit([
            'title' => 'Un super titre',
            'user' => $user,
            'album' => $album,
            'file' => $this->validFile,
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $this->assertEquals('Un super titre', $media->getTitle());
        $this->assertSame($user, $media->getUser());
        $this->assertSame($album, $media->getAlbum());
        $this->assertInstanceOf(UploadedFile::class, $media->getFile());
    }

    protected function getExtensions(): array
    {
        // ici, tu peux injecter d'autres types si tu as des formulaires custom
        return [
            new PreloadedExtension([], []),
        ];
    }
}

