<?php

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserValidationTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel(); // Nécessite le kernel
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testInvalidEmail(): void
    {
        $user = new User();
        $user->setEmail('pasunemail');
        $user->setLastName('Z'); // trop court
        $user->setPassword('xxx'); // peu importe ici
        $user->setRoles(['ROLE_USER']);

        $violations = $this->validator->validate($user);

        $this->assertCount(2, $violations); // Email invalide + lastName trop court

        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getMessage();
        }

        $this->assertContains("Veuiller entrer un email valide (nom@zaoui.com).", $messages);
        $this->assertContains("Cette valeur est trop courte. Elle doit avoir au minimum 2 caractères.", $messages);
    }

    public function testValidUser(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setLastName('Dupont');
        $user->setPassword('hashedpass');
        $user->setRoles(['ROLE_ADMIN']);

        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }
}
