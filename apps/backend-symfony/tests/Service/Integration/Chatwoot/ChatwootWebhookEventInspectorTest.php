<?php

namespace App\Tests\Service\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootMessageEvent;
use App\Service\Integration\Chatwoot\ChatwootWebhookEventInspector;
use PHPUnit\Framework\TestCase;

final class ChatwootWebhookEventInspectorTest extends TestCase
{
    private ChatwootWebhookEventInspector $inspector;

    protected function setUp(): void
    {
        $this->inspector = new ChatwootWebhookEventInspector();
    }

    public function testNormalizesEventType(): void
    {
        $this->assertSame('message_created', $this->inspector->normalizeEventType(' Message-Created '));
    }

    public function testIncomingMessageCreatedIsProcessable(): void
    {
        $event = $this->event('message_created', [
            'event' => 'message_created',
            'message' => [
                'id' => 123,
                'message_type' => 'incoming',
                'content' => 'Preciso de atendimento',
            ],
            'conversation' => ['id' => 456],
        ]);

        $this->assertNull($this->inspector->processabilityReason($event));
    }

    public function testUnsupportedEventIsIgnored(): void
    {
        $event = $this->event('contact_updated', ['event' => 'contact_updated']);

        $this->assertSame('Evento Chatwoot nao processavel: contact_updated.', $this->inspector->processabilityReason($event));
    }

    public function testPrivateOutgoingMessageIsIgnoredToAvoidLoop(): void
    {
        $event = $this->event('message_created', [
            'event' => 'message_created',
            'message' => [
                'id' => 123,
                'message_type' => 'outgoing',
                'private' => true,
                'content' => 'Protocolo SIGI gerado automaticamente: 202607150001',
            ],
            'conversation' => ['id' => 456],
        ]);

        $this->assertSame('Mensagem enviada pelo proprio SIGI ignorada para evitar loop.', $this->inspector->processabilityReason($event));
    }

    public function testPublicProtocolMessageIsIgnoredToAvoidLoop(): void
    {
        $event = $this->event('message_created', [
            'event' => 'message_created',
            'message' => [
                'id' => 123,
                'message_type' => 'outgoing',
                'private' => false,
                'content' => 'Ola! Recebemos sua solicitacao. Seu numero de protocolo e: 202607150001.',
            ],
            'conversation' => ['id' => 456],
        ]);

        $this->assertTrue($this->inspector->isSigiGeneratedMessage($event));
        $this->assertSame('Mensagem enviada pelo proprio SIGI ignorada para evitar loop.', $this->inspector->processabilityReason($event));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function event(?string $eventType, array $payload): ChatwootMessageEvent
    {
        return (new ChatwootMessageEvent())
            ->setEventType($eventType)
            ->setPayloadHash(hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR)))
            ->setRawPayload($payload);
    }
}