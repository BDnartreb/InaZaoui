<?php

namespace App\Tests\Unit;

use App\Entity\Media;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MediaTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testInvalidFileUpload(): void
    {
        $media = new Media();
        $media->setTitle('Test image');
      
        $tempPath = sys_get_temp_dir() . '/fake_large_image.jpg';
        file_put_contents($tempPath, str_repeat('0', 2 * 1024 * 1024 + 1));
        
        $this->assertFileExists($tempPath);

        $file = new UploadedFile(
            $tempPath,
            'fake_large_image.jpg',
            'image/jpeg',
            null,
            true
        );

        $media->setFile($file);
        $media->setUser((new User())->setEmail('test@ex.com')->setLastName('userforupload')->setPassword('password')->setRoles(['ROLE_USER']));

        $violations = $this->validator->validate($media);

        $this->assertGreaterThan(0, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Le fichier ne peut pas dépasser 2 Mo.', $messages);
        @unlink($tempPath);
    }
}
