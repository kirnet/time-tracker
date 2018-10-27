<?php

// generated by `make:user:msgphp`
// this configuration may be merged into your existing application configuration

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function (ContainerConfigurator $container) {
    $container->extension('msgphp_user', [
        'class_mapping'  =>
            [
                'MsgPhp\\User\\Entity\\Role'     => 'App\\Entity\\User\\Role',
                'MsgPhp\\User\\Entity\\UserRole' => 'App\\Entity\\User\\UserRole',
            ],
        'role_providers' =>
            [
                'default' =>
                    [
                        0 => 'ROLE_USER',
                        1 => 'ROLE_ADMIN',
                        2 => 'ROLE_MANAGER',
                    ],
                0         => 'MsgPhp\\User\\Role\\UserRoleProvider',
            ],
    ]);

    $container->services()
        ->defaults()
        ->private()
        ->autoconfigure()
        ->autowire()
        ->set(App\Console\ClassContextElementFactory::class)
        ->alias(MsgPhp\Domain\Infra\Console\Context\ClassContextElementFactoryInterface::class,
                  App\Console\ClassContextElementFactory::class);
};
