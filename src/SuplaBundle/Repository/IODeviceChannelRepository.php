<?php
namespace SuplaBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use SuplaBundle\Entity\DirectLink;
use SuplaBundle\Entity\IODeviceChannel;
use SuplaBundle\Entity\Schedule;

class IODeviceChannelRepository extends EntityWithRelationsRepository {
    protected $alias = 'c';

    protected function getEntityWithRelationsCountQuery(): QueryBuilder {
        return $this->_em->createQueryBuilder()
            ->addSelect('c entity')
            ->addSelect('COUNT(DISTINCT cg) channelGroups')
            ->addSelect(sprintf('(SELECT COUNT(1) FROM %s dl WHERE dl.channel = c) directLinks', DirectLink::class))
            ->addSelect(sprintf('(SELECT COUNT(1) FROM %s s WHERE s.channel = c) schedules', Schedule::class))
            ->from(IODeviceChannel::class, 'c')
            ->leftJoin('c.channelGroups', 'cg')
            ->groupBy('c.id');
    }
}
