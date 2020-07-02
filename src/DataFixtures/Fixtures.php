<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Persistence\ObjectManager;

class Fixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * Fixtures constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $entity = new User();
        $entity->setEmail('admin@admin.by');
        $entity->setLogin('admin');
        $entity->setIsBanned(0);
        $entity->setRegisterAt(new DateTime());
        $entity->setPassword($this->passwordEncoder->encodePassword($entity, '111111'));
        $entity->setRoles(['ROLE_ADMIN']);
        $manager->persist($entity);

        $entity = new User();
        $entity->setEmail('user@user.by');
        $entity->setLogin('user');
        $entity->setIsBanned(0);
        $entity->setRegisterAt(new DateTime());
        $entity->setPassword($this->passwordEncoder->encodePassword($entity, '111111'));
        $entity->setRoles(['ROLE_USER']);
        $manager->persist($entity);

        for ($i = 0; $i < 100; $i++) {
            $entity = new User();
            $entity->setEmail("user{$i}@user.by");
            $entity->setLogin("user{$i}");
            $entity->setIsBanned(0);
            $entity->setRegisterAt(new DateTime());
            $entity->setPassword($this->passwordEncoder->encodePassword($entity, '111111'));
            $entity->setRoles(['ROLE_USER']);
            $manager->persist($entity);
        }

        $entity = new User();
        $entity->setEmail("test@test.by");
        $entity->setLogin("test");
        $entity->setIsBanned(0);
        $entity->setRegisterAt(new DateTime());
        $entity->setPassword($this->passwordEncoder->encodePassword($entity, '111111'));
        $entity->setRoles(['ROLE_USER']);
        $manager->persist($entity);

        $manager->flush();

//        $entity = new Project();
//        $entity->setName('Admin project');
//        $entity->setDescription('Description admin project');
//        $entity->setCreatedAt(new \DateTime());
//        $entity->setOwner(1);
//        $manager->persist($entity);
//        $manager->flush();
    }
}
