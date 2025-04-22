<?php

namespace App\Tests\Unit;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
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
        $user->setEmail('badEmail');
        $user->setFirstName('userFirstName');
        $user->setLastName('');
        $user->setPassword('xxx');
        $user->setRoles(['ROLE_USER']);

        $violations = $this->validator->validate($user);

        $this->assertCount(3, $violations);

        $messages = [];
        foreach ($violations as $violation) {
            $messages[] = $violation->getMessage();
        }

        $this->assertContains("Veuiller entrer un email valide (nom@zaoui.com).", $messages);
        $this->assertContains("Veuillez entrer votre nom.", $messages);
    }

    public function testValidUser(): void
    {
        $user = new User();
        $user->setEmail('user@zaoui.com');
        $user->setFirstName('');
        $user->setLastName('userLastName');
        $user->setPassword('password');
        $user->setRoles(['ROLE_ADMIN']);

        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }
}
