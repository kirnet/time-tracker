<?php

namespace Form;

use App\Entity\Period;
use App\Form\PeriodType;
use Symfony\Component\Form\Test\TypeTestCase;

class PeriodTypeTest extends TypeTestCase
{
    public function testAddFormPeriod()
    {
        $now = new \DateTime();
        $formData = [
            'name' => 'test period',
            'time_start' => $now->format('Y-m-d H:i:s'),
            'alert_time' => $now->add(new \DateInterval('PT1M'))->format('Y-m-d H:i:s'),
        ];

        // $formData will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(PeriodType::class);

        $form->submit($formData);
        $form->isValid();
        $dataReceive = $form->getData();

        $object = new Period();
        $object->setName($dataReceive->getName());
        $object->setTimeStart($dataReceive->getTimeStart());
        $object->setAlertTime($dataReceive->getAlertTime());
        $object->setCreatedAt($dataReceive->getCreatedAt());

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $dataReceive);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
