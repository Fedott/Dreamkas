<?php

namespace Lighthouse\JobBundle\Worker;

use JMS\DiExtraBundle\Annotation as DI;
use Lighthouse\JobBundle\Document\Job\Job;
use Lighthouse\CoreBundle\Exception\RuntimeException;
use Lighthouse\JobBundle\Worker\WorkerInterface;

/**
 * @DI\Service("lighthouse.core.job.worker.manager");
 */
class WorkerManager
{
    /**
     * @var WorkerInterface[]
     */
    protected $workers = array();

    /**
     * @param WorkerInterface $worker
     */
    public function add(WorkerInterface $worker)
    {
        $this->workers[$worker->getName()] = $worker;
    }

    /**
     * @return array|WorkerInterface[]
     */
    public function getAll()
    {
        return $this->workers;
    }

    /**
     * @return array
     */
    public function getNames()
    {
        return array_keys($this->workers);
    }

    /**
     * @param Job $job
     * @return WorkerInterface
     * @throws RuntimeException
     */
    public function getByJob(Job $job)
    {
        foreach ($this->workers as $worker) {
            if ($worker->supports($job)) {
                return $worker;
            }
        }

        throw new RuntimeException(sprintf('No worker found for job %s', get_class($job)));
    }
}
