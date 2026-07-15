<?php

namespace App\Tests\Service\Conversation;

use App\Entity\Conversation;
use App\Entity\ServiceRequest;
use App\Service\Conversation\ServiceRequestTransitionService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\Exception\LogicException;

final class ServiceRequestTransitionServiceTest extends KernelTestCase
{
    private ServiceRequestTransitionService $transitionService;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->transitionService = self::getContainer()->get(ServiceRequestTransitionService::class);
    }

    public function testAppliesValidTransition(): void
    {
        $request = $this->newRequest();

        $this->transitionService->apply($request, 'start_collection');

        $this->assertSame('collecting', $request->getCurrentState());
        $this->assertSame(ServiceRequest::STATUS_OPEN, $request->getStatus());
    }

    public function testRejectsInvalidTransition(): void
    {
        $request = $this->newRequest();

        $this->expectException(LogicException::class);
        $this->transitionService->apply($request, 'complete');
    }

    public function testGuardBlocksAutomationWhenHumanControlsConversation(): void
    {
        $conversation = (new Conversation())
            ->setChatwootAccountId(1)
            ->setChatwootConversationId('1001')
            ->setCurrentController(Conversation::CONTROLLER_HUMAN)
            ->setAutomationEnabled(false);
        $request = $this->newRequest($conversation);

        $this->assertFalse($this->transitionService->can($request, 'start_collection'));
        $this->assertTrue($this->transitionService->can($request, 'request_human'));

        $this->transitionService->apply($request, 'request_human');

        $this->assertSame('awaiting_human', $request->getCurrentState());
    }

    public function testLowConfidenceRoutesToHuman(): void
    {
        $request = $this->newRequest();

        $routed = $this->transitionService->routeLowConfidence($request, 0.42);

        $this->assertTrue($routed);
        $this->assertSame(0.42, $request->getConfidence());
        $this->assertSame('awaiting_human', $request->getCurrentState());
    }

    public function testAcceptableConfidenceDoesNotRouteToHuman(): void
    {
        $request = $this->newRequest();

        $routed = $this->transitionService->routeLowConfidence($request, ServiceRequestTransitionService::LOW_CONFIDENCE_THRESHOLD);

        $this->assertFalse($routed);
        $this->assertSame(ServiceRequestTransitionService::LOW_CONFIDENCE_THRESHOLD, $request->getConfidence());
        $this->assertSame(ServiceRequest::DEFAULT_STATE, $request->getCurrentState());
    }

    private function newRequest(?Conversation $conversation = null): ServiceRequest
    {
        $conversation ??= (new Conversation())
            ->setChatwootAccountId(1)
            ->setChatwootConversationId('1000');

        return (new ServiceRequest())
            ->setConversation($conversation)
            ->setProtocol('202607150001');
    }
}