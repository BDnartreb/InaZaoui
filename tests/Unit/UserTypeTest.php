<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserUpdateType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class UserTypeTest extends TypeTestCase
{

    /**
    * @return array<\Symfony\Component\Form\FormExtensionInterface>
    */
    protected function getExtensions(): array
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testUserFormSubmitValidData(): void
    {
        $email = "userTypeTest@zaoui.com";
        $firstName = "userTypeTestFirstName";
        $lastname = "userTypeTestLastName";
        $description = "Test User Type";

        $formData = [
            'email' => $email,
            'firstname' => $firstName,
            'lastname' => $lastname,
            'description' => $description,
        ];

        $model = new User();

        $form = $this->factory->create(UserType::class, $model);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized(), 'Le formulaire n’est pas synchronisé.');
        $this->assertTrue($form->isValid(), 'Le formulaire est valide.');

        $this->assertEquals($email, $model->getEmail());
        $this->assertEquals($firstName, $model->getFirstname());
        $this->assertEquals($lastname, $model->getLastname());
        $this->assertEquals($description, $model->getDescription());
    }

    public function testUpdateUserFormSubmitValidData(): void
    {
        $email = "userTypeTest@zaoui.com";
        $roles = ['ROLE_FROZEN'];
        $password = 'password';
        $updatedFirstName = "updateUserTypeTestFirstName";
        $updatedLastname = "updateUserTypeTestLastName";
        $updatedDescription = "Updated Test User Type";


        $formData = [
            'email' => $email,
            'roles' => $roles,
            'password' => $password,
            'firstName' => $updatedFirstName,
            'lastName' => $updatedLastname,
            'description' => $updatedDescription,
        ];

        $user = new User();

        $form = $this->factory->create(UserUpdateType::class, $user);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized(), 'Le formulaire n’est pas synchronisé');
        $this->assertTrue($form->isValid(), 'Le formulaire n’est pas valide');

        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($roles, $user->getRoles());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($updatedFirstName, $user->getFirstName());
        $this->assertEquals($updatedLastname, $user->getLastName());
        $this->assertEquals($updatedDescription, $user->getDescription());
    }
}
