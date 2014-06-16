<?php

namespace Lighthouse\CoreBundle\Job;

use Lighthouse\CoreBundle\Document\Job\Job;
use Lighthouse\CoreBundle\Job\Worker\WorkerManager;
use Lighthouse\CoreBundle\Exception\Job\NotFoundJobException;
use Lighthouse\CoreBundle\Security\Project\ProjectContext;
use Pheanstalk_PheanstalkInterface as PheanstalkInterface;
use Pheanstalk_Exception_ServerException as PheanstalkServerException;
use JMS\DiExtraBundle\Annotation as DI;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * @DI\Service("lighthouse.core.job.manager")
 * @DI\Tag("monolog.logger", attributes={"channel"="job"})
 */
class JobManager
{
    /**
     * @var WorkerManager
     */
    protected $workerManager;

    /**
     * @var JobRepository
     */
    protected $jobRepository;

    /**
     * @var PheanstalkInterface
     */
    protected $pheanstalk;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ProjectContext
     */
    protected $projectContext;

    /**
     * @DI\InjectParams({
     *      "pheanstalk" = @DI\Inject("leezy.pheanstalk"),
     *      "jobRepository" = @DI\Inject("lighthouse.core.job.repository"),
     *      "workerManager" = @DI\Inject("lighthouse.core.job.worker.manager"),
     *      "logger" = @DI\Inject("logger"),
     *      "projectContext" = @DI\Inject("project.context"),
     * })
     * @param PheanstalkInterface $pheanstalk
     * @param JobRepository $jobRepository
     * @param WorkerManager $workerManager
     * @param LoggerInterface $logger
     * @param ProjectContext $projectContext
     */
    public function __construct(
        PheanstalkInterface $pheanstalk,
        JobRepository $jobRepository,
        WorkerManager $workerManager,
        LoggerInterface $logger,
        ProjectContext $projectContext
    ) {
        $this->pheanstalk = $pheanstalk;
        $this->jobRepository = $jobRepository;
        $this->workerManager = $workerManager;
        $this->logger = $logger;
        $this->projectContext = $projectContext;
    }

    /**
     * @param Job $job
     */
    public function addJob(Job $job)
    {
        if (! $job->silent) {
            // save job if it was not saved before
            $this->jobRepository->save($job);
        }

        $jobId = $this->putJobInTube($job);

        if (! $job->silent) {
            $job->setPendingStatus($jobId);
            $this->jobRepository->save($job);
        }
    }

    /**
     * @param Job $job
     * @return int tube job id
     */
    protected function putJobInTube(Job $job)
    {
        $worker = $this->workerManager->getByJob($job);
        $tubeName = $worker->getName();
        $data = $job->getTubeData() + array(
            'projectId' => $this->projectContext->getCurrentProject()->getName(),
        );
        $jsonData = json_encode($data);
        return $this->pheanstalk->putInTube($tubeName, $jsonData);
    }

    /**
     * @return $this
     */
    public function startWatchingTubes()
    {
        foreach ($this->workerManager->getNames() as $tubeName) {
            $this->pheanstalk->watch($tubeName);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function stopWatchingTubes()
    {
        foreach ($this->workerManager->getNames() as $tubeName) {
            $this->pheanstalk->ignore($tubeName);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function purgeTubes()
    {
        $this->purgeReserved();
        $this->purgeReady();
        $this->purgeDelayed();
        $this->purgeBuried();

        return $this;
    }

    /**
     *
     */
    protected function purgeReserved()
    {
        while (true) {
            $job = $this->pheanstalk->reserve(0);
            if (false === $job) {
                break;
            }
            $this->pheanstalk->delete($job);
        }
    }

    /**
     *
     */
    protected function purgeReady()
    {
        try {
            while (true) {
                $job = $this->pheanstalk->peekReady();
                $this->pheanstalk->delete($job);
            }
        } catch (PheanstalkServerException $e) {
        }
    }

    /**
     *
     */
    protected function purgeDelayed()
    {
        try {
            while (true) {
                $job = $this->pheanstalk->peekDelayed();
                $this->pheanstalk->delete($job);
            }
        } catch (PheanstalkServerException $e) {
        }
    }

    /**
     *
     */
    protected function purgeBuried()
    {
        try {
            while (true) {
                $job = $this->pheanstalk->peekBuried();
                $this->pheanstalk->delete($job);
            }
        } catch (PheanstalkServerException $e) {
        }
    }

    /**
     * @param int|null $timeout
     * @return Job|null
     * @throws NotFoundJobException
     */
    public function reserveJob($timeout = null)
    {
        /* @var \Pheanstalk_Job $tubeJob */
        $tubeJob = $this->pheanstalk->reserve($timeout);
        if (!$tubeJob) {
            return null;
        }

        $data = json_decode($tubeJob->getData(), true);
        $silent = $data['silent'];
        $jobId = $data['jobId'];
        $projectId = $data['projectId'];

        if (null === $this->projectContext->getCurrentProject()) {
            $this->projectContext->authenticateByProjectName($projectId);
        }

        if ($silent) {
            return $this->reserveSilentJob($data, $tubeJob);
        }

        /* @var Job $job */
        $job = $this->jobRepository->find($jobId);
        if (!$job) {
            $this->logger->critical(
                sprintf(
                    'Job #%s from tube was not found in repository by id #%s. Job will be deleted from tube.',
                    $tubeJob->getId(),
                    $jobId
                )
            );
            $this->pheanstalk->delete($tubeJob);
            throw new NotFoundJobException($jobId, $tubeJob->getId());
        }
        $job->setTubeJob($tubeJob);

        $job->setProcessingStatus();
        $this->jobRepository->save($job);

        if (null === $this->projectContext->getCurrentProject()) {
            $this->projectContext->logout();
        }

        return $job;
    }

    /**
     * @param array $data
     * @param \Pheanstalk_Job $tubeJob
     * @return Job
     */
    protected function reserveSilentJob(array $data, $tubeJob)
    {
        $jobClassName = $data['className'];
        /** @var Job $job */
        $job = new $jobClassName;

        $job->setDataFromTube($data);
        $job->jobId = $tubeJob->getId();
        $job->setTubeJob($tubeJob);

        return $job;
    }

    /**
     * @param Job $job
     */
    public function processJob(Job $job)
    {
        try {
            $worker = $this->workerManager->getByJob($job);
            $worker->work($job);

            $this->pheanstalk->delete($job->getTubeJob());

            if (! $job->silent) {
                $job->setSuccessStatus();
                $this->jobRepository->save($job);
            }

        } catch (Exception $e) {
            $this->logger->emergency($e);

            $this->pheanstalk->delete($job->getTubeJob());

            if (! $job->silent) {
                $job->setFailStatus($e->getMessage());
                $this->jobRepository->save($job);
            }
        }
    }
}
