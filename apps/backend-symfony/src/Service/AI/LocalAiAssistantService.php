<?php

namespace App\Service\AI;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class LocalAiAssistantService
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * @param array<string, mixed>        $conversation
     * @param array<int, array<string, mixed>> $messages
     */
    public function suggestReply(array $conversation, array $messages, ?string $instruction = null): string
    {
        $prompt = $this->buildPrompt($conversation, $messages, $instruction);

        try {
            $response = $this->httpClient->request('POST', $this->getOllamaBaseUrl().'/api/generate', [
                'json' => [
                    'model' => $this->getOllamaModel(),
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.3,
                    ],
                ],
                'timeout' => 60,
            ]);

            $payload = json_decode($response->getContent(false), true);
            if (is_array($payload) && isset($payload['response']) && is_scalar($payload['response'])) {
                $text = trim((string) $payload['response']);
                if ('' !== $text) {
                    return $text;
                }
            }
        } catch (TransportExceptionInterface|\Throwable) {
        }

        return "Olá, obrigado pelo contato.\n\nRecebemos sua solicitação e nossa equipe dará continuidade ao atendimento por este canal.";
    }

    /**
     * @param array<string, mixed>        $conversation
     * @param array<int, array<string, mixed>> $messages
     */
    private function buildPrompt(array $conversation, array $messages, ?string $instruction): string
    {
        $subject = $this->scalar($conversation['subject'] ?? null) ?? 'Sem assunto';
        $status = $this->scalar($conversation['status'] ?? null) ?? 'Nao informado';
        $labels = $conversation['labels'] ?? [];
        $labelsText = is_array($labels) ? implode(', ', array_filter(array_map([$this, 'scalar'], $labels))) : '';
        $transcript = $this->buildTranscript($messages);

        return <<<PROMPT
Voce e um assistente local do SIGI-SD ajudando um atendente humano no Chatwoot.
Escreva uma resposta curta, educada, objetiva e em portugues do Brasil.
Nao invente dados. Se faltar informacao, peca os dados necessarios.

Assunto: {$subject}
Status: {$status}
Etiquetas: {$labelsText}
Instrucao do operador: {$instruction}

Historico recente:
{$transcript}

Resposta sugerida:
PROMPT;
    }

    /**
     * @param array<int, array<string, mixed>> $messages
     */
    private function buildTranscript(array $messages): string
    {
        $lines = [];
        foreach (array_slice($messages, -12) as $message) {
            $content = $this->scalar($message['content'] ?? null);
            if (null === $content || '' === trim($content)) {
                continue;
            }

            $private = (bool) ($message['private'] ?? false);
            $type = $this->scalar($message['message_type'] ?? null) ?? 'mensagem';
            $sender = $this->senderName($message);
            $prefix = $private ? 'nota privada' : $type;
            $lines[] = sprintf('%s - %s: %s', $prefix, $sender, mb_substr(trim($content), 0, 1200));
        }

        return [] === $lines ? 'Sem mensagens recentes disponiveis.' : implode("\n", $lines);
    }

    /**
     * @param array<string, mixed> $message
     */
    private function senderName(array $message): string
    {
        $sender = $message['sender'] ?? null;
        if (is_array($sender)) {
            return $this->scalar($sender['name'] ?? null) ?? $this->scalar($sender['available_name'] ?? null) ?? 'desconhecido';
        }

        return 'desconhecido';
    }

    private function scalar(mixed $value): ?string
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return '' === $value ? null : $value;
    }

    private function getOllamaBaseUrl(): string
    {
        $value = $_ENV['OLLAMA_BASE_URL'] ?? $_SERVER['OLLAMA_BASE_URL'] ?? getenv('OLLAMA_BASE_URL') ?: 'http://ollama:11434';

        return rtrim((string) $value, '/');
    }

    private function getOllamaModel(): string
    {
        $value = $_ENV['OLLAMA_MODEL'] ?? $_SERVER['OLLAMA_MODEL'] ?? getenv('OLLAMA_MODEL') ?: 'llama3.1';

        return trim((string) $value);
    }
}

