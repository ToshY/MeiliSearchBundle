<?php

declare(strict_types=1);

namespace MeiliSearchBundle\DataCollector;

use MeiliSearchBundle\Client\InstanceProbeInterface;
use MeiliSearchBundle\Client\TraceableDocumentOrchestrator;
use MeiliSearchBundle\Client\TraceableIndexOrchestrator;
use MeiliSearchBundle\Search\TraceableSearchEntryPoint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

/**
 * @author Guillaume Loulier <contact@guillaumeloulier.fr>
 */
final class MeiliSearchBundleDataCollector extends DataCollector implements LateDataCollectorInterface
{
    private const NAME = 'meili';

    /**
     * @var TraceableIndexOrchestrator
     */
    private $indexOrchestrator;

    /**
     * @var InstanceProbeInterface
     */
    private $instanceProbe;

    /**
     * @var TraceableDocumentOrchestrator
     */
    private $documentOrchestrator;

    /**
     * @var TraceableSearchEntryPoint
     */
    private $searchEntryPoint;

    public function __construct(
        InstanceProbeInterface $instanceProbe,
        TraceableIndexOrchestrator $indexOrchestrator,
        TraceableDocumentOrchestrator $documentOrchestrator,
        TraceableSearchEntryPoint $searchEntryPoint
    ) {
        $this->instanceProbe = $instanceProbe;
        $this->indexOrchestrator = $indexOrchestrator;
        $this->documentOrchestrator = $documentOrchestrator;
        $this->searchEntryPoint = $searchEntryPoint;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        $this->data['system_info'] = $this->instanceProbe->getSystemInformations();
        $this->data['indexes'] = $this->indexOrchestrator->getIndexes();
        $this->data['queries'] = $this->searchEntryPoint->getSearch();
        $this->data['created_indexes'] = $this->indexOrchestrator->getCreatedIndexes();
        $this->data['deleted_indexes'] = $this->indexOrchestrator->getDeletedIndexes();
        $this->data['fetched_indexes'] = $this->indexOrchestrator->getFetchedIndexes();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    public function getSystemInformations(): array
    {
        return $this->data['system_info'];
    }

    public function getIndexes(): array
    {
        return $this->data['indexes'];
    }

    public function getCreatedIndexes(): array
    {
        return $this->data['created_indexes'];
    }

    public function getDeletedIndexes(): array
    {
        return $this->data['deleted_indexes'];
    }

    public function getFetchedIndexes(): array
    {
        return $this->data['fetched_indexes'];
    }

    public function getQueriesCount(): int
    {
        return \count($this->searchEntryPoint->getSearch());
    }
}
