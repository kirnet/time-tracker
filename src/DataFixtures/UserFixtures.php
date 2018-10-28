<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('admin@admin.by');
        $user->setLogin('admin');
        $user->setIsBanned(0);
        $user->setRegisterAt(new \DateTime());
        $user->setPassword($this->passwordEncoder->encodePassword($user, '111111'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setEmail('user@user.by');
        $user->setLogin('user');
        $user->setIsBanned(0);
        $user->setRegisterAt(new \DateTime());
        $user->setPassword($this->passwordEncoder->encodePassword($user, '111111'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);
        $manager->flush();
    }
}
