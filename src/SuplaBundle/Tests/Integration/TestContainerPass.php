<?php
namespace SuplaBundle\Tests\Integration;

use Assert\Assertion;
use SuplaBundle\EventListener\ApiRateLimit\GlobalApiRateLimit;
use SuplaBundle\EventListener\UnavailableInMaintenanceRequestListener;
use SuplaBundle\Model\Audit\Audit;
use SuplaBundle\Model\ChannelParamsUpdater\ChannelParamsUpdater;
use SuplaBundle\Model\Schedule\ScheduleManager;
use SuplaBundle\Model\UserManager;
use SuplaBundle\Repository\ApiClientRepository;
use SuplaBundle\Repository\AuditEntryRepository;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Makes enumerated private services that needs to be tested public so they can be fetched from the container without a deprecation warning.
 *
 * @see https://github.com/symfony/symfony-docs/issues/8097
 * @see https://github.com/symfony/symfony/issues/24543
 */
class TestContainerPass implements CompilerPassInterface {
    private static $publicInTests = [
        'test.client',
        'location_manager',
        Audit::class,
        ChannelParamsUpdater::class,
        ScheduleManager::class,
        UserManager::class,
        GlobalApiRateLimit::class,
        UnavailableInMaintenanceRequestListener::class,
        ApiClientRepository::class,
        AuditEntryRepository::class,
    ];

    public function process(ContainerBuilder $container) {
        $madePublic = [];
        foreach ($container->getDefinitions() as $id => $definition) {
            if (in_array($id, self::$publicInTests, true) || in_array($definition->getClass(), self::$publicInTests, true)) {
                $definition->setPublic(true);
                $madePublic[] = $id;
            }
        }
        foreach ($container->getAliases() as $id => $definition) {
            if (in_array($id, self::$publicInTests, true)) {
                $definition->setPublic(true);
                $madePublic[] = $id;
            }
        }
        Assertion::count($madePublic, count(self::$publicInTests), function () use ($madePublic) {
            return 'The following services were not made public although they have been requested: '
                . implode(', ', array_diff(self::$publicInTests, $madePublic));
        });
    }
}
