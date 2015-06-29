<?php

namespace Lighthouse\ReportsBundle\Document\Gross\Hour\Network;

use Lighthouse\ReportsBundle\Document\Gross\Hour\HourGross;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(
 *      repositoryClass="Lighthouse\ReportsBundle\Document\Gross\Hour\Network\GrossHourNetworkRepository",
 *      collection="Gross.Hour.Network"
 * )
 * @MongoDB\Index(keys={"hourDate"="asc"})
 */
class GrossHourNetwork extends HourGross
{
}
