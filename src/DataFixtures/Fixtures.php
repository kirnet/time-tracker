<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Fixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface  */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $entity = new User();
        $entity->setEmail('admin@admin.by');
        $entity->setLogin('admin');
        $entity->setIsBanned(0);
        $entity->setRegisterAt(new \DateTime());
        $entity->setPassword($this->passwordEncoder->encodePassword($entity, '111111'));
        $entity->setRoles(['ROLE_ADMIN']);
        $manager->persist($entity);
        $manager->flush();

        $entity = new User();
        $entity->setEmail('user@user.by');
        $entity->setLogin('user');
        $entity->setIsBanned(0);
        $entity->setRegisterAt(new \DateTime());
        $entity->setPassword($this->passwordEncoder->encodePassword($entity, '111111'));
        $entity->setRoles(['ROLE_USER']);
        $manager->persist($entity);
        $manager->flush();

        $entity = new Project();
        $entity->setName('Admin project');
        $entity->setDescription('Description admin project');
        $entity->setCreatedAt(new \DateTime());
        $entity->setUser(1);
        $manager->persist($entity);
        $manager->flush();
    }
}
