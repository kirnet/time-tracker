<?php

namespace Form;

use App\Entity\User;
use App\Form\UserType;

use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends  TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'login' => 'testLogin',
            'email' => 'testEmail@mail.ru',
            'password' => '111111'
        ];

        $form = $this->factory->create(UserType::class);
        $form->submit($formData);

        $object = new User();
        $object->setLogin('testLogin');
        $object->setEmail('testEmail@mail.ru');
        $object->setIsBanned(false);
        $dataReceive = $form->getData();
        $object->setRegisterAt($dataReceive->getRegisterAt());

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $dataReceive);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}