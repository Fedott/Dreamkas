<?php

namespace Lighthouse\JobBundle\Job;

use Lighthouse\JobBundle\Document\Job\Job;
use Lighthouse\JobBundle\Worker\WorkerManager;
use Lighthouse\JobBundle\Exception\NotFoundJobException;
use Lighthouse\CoreBundle\Security\Project\ProjectContext;
use Lighthouse\JobBundle\Document\Job\JobRepository;
use Pheanstalk_Job;
use Pheanstalk_PheanstalkInterface as PheanstalkInterface;
use Pheanstalk_Exception_ServerException as PheanstalkServerException;
use JMS\DiExtraBundle\Annotation as DI;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * @DI\Service("lighthouse.job.manager")
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
     *      "jobRepository" = @DI\Inject("lighthouse.job.repository"),
     *      "workerManager" = @DI\Inject("lighthouse.job.worker.manager"),
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
        $this->saveJobIfNeeded($job);

        $jobId = $this->putJobInTube($job);

        $job->setPendingStatus($jobId);

        $this->saveJobIfNeeded($job);
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
        /* @var Pheanstalk_Job $tubeJob */
        $tubeJob = $this->pheanstalk->reserve($timeout);
        if (!$tubeJob) {
            return null;
        }

        $data = json_decode($tubeJob->getData(), true);

        $projectId = $data['projectId'];
        if (null === $this->projectContext->getCurrentProject()) {
            $this->projectContext->authenticateByProjectName($projectId);
        }

        $job = $this->getJob($tubeJob);

        $job->setTubeJob($tubeJob);

        $job->setProcessingStatus();
        $this->saveJobIfNeeded($job);

        if (null === $this->projectContext->getCurrentProject()) {
            $this->projectContext->logout();
        }

        return $job;
    }

    /**
     * @param Pheanstalk_Job $tubeJob
     * @return Job
     */
    public function getJob(Pheanstalk_Job $tubeJob)
    {
        $jobData = $this->getJobDataArray($tubeJob);
        if (isset($jobData['persist']) && $jobData['persist']) {
            $job = $this->getPersistJob($tubeJob);
        } else {
            $job = $this->getNotPersistJob($tubeJob);
        }

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

            $job->setSuccessStatus();
            $this->saveJobIfNeeded($job);
        } catch (Exception $e) {
            $this->logger->emergency($e);

            $this->pheanstalk->delete($job->getTubeJob());

            $job->setFailStatus($e->getMessage());
            $this->saveJobIfNeeded($job);
        }
    }

    /**
     * @param Job $job
     */
    protected function saveJobIfNeeded(Job $job)
    {
        if ($job->isPersist()) {
            $this->jobRepository->save($job);
        }
    }

    /**
     * @param Pheanstalk_Job $tubeJob
     * @return array
     */
    protected function getJobDataArray(Pheanstalk_Job $tubeJob)
    {
        return json_decode($tubeJob->getData(), true);
    }

    /**
     * @param Pheanstalk_Job $tubeJob
     * @return Job
     */
    protected function getPersistJob(Pheanstalk_Job $tubeJob)
    {
        $tubeJobData = $this->getJobDataArray($tubeJob);
        $jobId = $tubeJobData['jobId'];
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

        return $job;
    }

    /**
     * @param Pheanstalk_Job $tubeJob
     * @return Job
     */
    protected function getNotPersistJob(Pheanstalk_Job $tubeJob)
    {
        $tubeJobData = $this->getJobDataArray($tubeJob);

        $jobClassName = $tubeJobData['className'];
        /** @var Job $job */
        $job = new $jobClassName;

        $job->setDataFromTube($tubeJobData);
        $job->jobId = $tubeJob->getId();

        return $job;
    }
}
