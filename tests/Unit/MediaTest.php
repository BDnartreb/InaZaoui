<?php

use App\Entity\Media;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MediaValidationTest extends KernelTestCase
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
        $media->setPath('/fake/path.jpg');

        // Créer un faux fichier TXT (invalide)
        $file = new UploadedFile(
            __DIR__ . '/fixtures/fake.txt',
            'fake.txt',
            'text/plain',
            null,
            true // test mode
        );

        $media->setFile($file);
        $media->setUser((new User())->setEmail('test@ex.com')->setLastName('Doe')->setPassword('pwd')->setRoles(['ROLE_USER']));

        $violations = $this->validator->validate($media);

        $this->assertGreaterThan(0, count($violations));
        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $this->assertContains('Veuillez uploader un fichier PDF, JPG ou PNG.', $messages);
    }
}
