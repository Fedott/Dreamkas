<?php

namespace Lighthouse\CoreBundle\Document\CashFlow;

use DateTime;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Lighthouse\CoreBundle\Document\AbstractMongoDBListener;

/**
 * @DI\DoctrineMongoDBListener(events={"onFlush"})
 */
class CashFlowListener extends AbstractMongoDBListener
{
    /**
     * @var CashFlowRepository
     */
    protected $cashFlowRepository;

    /**
     * @DI\InjectParams({
     *      "cashFlowRepository" = @DI\Inject("lighthouse.core.document.repository.cash_flow"),
     * })
     *
     * @param CashFlowRepository $cashFlowRepository
     */
    public function __construct(CashFlowRepository $cashFlowRepository)
    {
        $this->cashFlowRepository = $cashFlowRepository;
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $dm = $eventArgs->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        foreach ($uow->getScheduledDocumentInsertions() as $document) {
            if ($document instanceof CashFlowable) {
                if ($document->cashFlowNeeded()) {
                    /** @var CashFlow $cashFlow */
                    $cashFlow = $this->cashFlowRepository->createNew();
                    $cashFlow->amount = $document->getCashFlowAmount();
                    $cashFlow->date = new DateTime();
                    $cashFlow->direction = $document->getCashFlowDirection();
                    $cashFlow->reason = $document;

                    $dm->persist($cashFlow);
                    $this->computeChangeSet($dm, $cashFlow);
                }
            }
        }

        foreach ($uow->getScheduledDocumentUpdates() as $document) {
            if ($document instanceof CashFlowable) {
                if ($document->cashFlowNeeded()) {
                    $cashFlow = $this->cashFlowRepository->findOneByReason($document);
                    if (null === $cashFlow) {
                        $cashFlow = $this->cashFlowRepository->createNew();
                        $cashFlow->amount = $document->getCashFlowAmount();
                        $cashFlow->date = new DateTime();
                        $cashFlow->direction = $document->getCashFlowDirection();
                        $cashFlow->reason = $document;
                    } else {
                        $cashFlow->amount = $document->getCashFlowAmount();
                        $cashFlow->direction = $document->getCashFlowDirection();
                    }

                    $dm->persist($cashFlow);
                    $this->computeChangeSet($dm, $cashFlow);
                } else {
                    $cashFlow = $this->cashFlowRepository->findOneByReason($document);
                    if (null !== $cashFlow) {
                        $dm->remove($cashFlow);
                    }
                }
            }
        }


        foreach ($uow->getScheduledDocumentDeletions() as $document) {
            if ($document instanceof CashFlowable) {
                $cashFlow = $this->cashFlowRepository->findOneByReason($document);
                $dm->remove($cashFlow);
            }
        }
    }
}
