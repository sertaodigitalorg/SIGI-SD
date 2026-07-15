<?php

namespace App\Service\Conversation;

use App\Entity\ServiceRequest;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Registry;

final readonly class ServiceRequestTransitionService
{
    public const LOW_CONFIDENCE_THRESHOLD = 0.65;

    public function __construct(private Registry $workflowRegistry)
    {
    }

    public function can(ServiceRequest $request, string $transition): bool
    {
        return $this->workflowRegistry->get($request, 'service_request')->can($request, $transition);
    }

    public function apply(ServiceRequest $request, string $transition): void
    {
        $workflow = $this->workflowRegistry->get($request, 'service_request');
        if (!$workflow->can($request, $transition)) {
            throw new LogicException(sprintf(
                'Transition "%s" cannot be applied from state "%s".',
                $transition,
                $request->getCurrentState(),
            ));
        }

        $workflow->apply($request, $transition);
        $this->syncStatus($request, $transition);
        $request->touch();
    }

    public function routeLowConfidence(ServiceRequest $request, ?float $confidence): bool
    {
        $request->setConfidence($confidence);
        if (null === $confidence || $confidence >= self::LOW_CONFIDENCE_THRESHOLD) {
            return false;
        }

        if (!$this->can($request, 'request_human')) {
            return false;
        }

        $this->apply($request, 'request_human');

        return true;
    }

    private function syncStatus(ServiceRequest $request, string $transition): void
    {
        match ($transition) {
            'complete' => $request->markCompleted(),
            'close' => $request->markClosed(),
            'cancel' => $request->setStatus(ServiceRequest::STATUS_CANCELLED),
            default => $request->setStatus(ServiceRequest::STATUS_OPEN),
        };
    }
}