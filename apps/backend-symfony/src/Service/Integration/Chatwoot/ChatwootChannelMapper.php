<?php

namespace App\Service\Integration\Chatwoot;

final class ChatwootChannelMapper
{
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_WHATSAPP = 'whatsapp';
    public const CHANNEL_INSTAGRAM = 'instagram';
    public const CHANNEL_WEBCHAT = 'webchat';
    public const CHANNEL_UNKNOWN = 'unknown';

    /**
     * @param array<int, string|null> $values
     */
    public function resolve(array $values): string
    {
        $haystack = mb_strtolower(implode(' ', array_filter(array_map(
            static fn (?string $value): ?string => null === $value ? null : trim($value),
            $values,
        ))));

        if ('' === $haystack) {
            return self::CHANNEL_UNKNOWN;
        }

        if (str_contains($haystack, 'whatsapp') || str_contains($haystack, 'channel::whatsapp')) {
            return self::CHANNEL_WHATSAPP;
        }

        if (str_contains($haystack, 'instagram') || str_contains($haystack, 'channel::instagram')) {
            return self::CHANNEL_INSTAGRAM;
        }

        if (str_contains($haystack, 'email') || str_contains($haystack, 'mail') || str_contains($haystack, 'channel::email')) {
            return self::CHANNEL_EMAIL;
        }

        if (str_contains($haystack, 'web_widget') || str_contains($haystack, 'webchat') || str_contains($haystack, 'website')) {
            return self::CHANNEL_WEBCHAT;
        }

        return self::CHANNEL_UNKNOWN;
    }

    /**
     * @return array<int, string>
     */
    public function defaultLabels(string $channel, bool $customerProtocolMessageSent): array
    {
        $labels = [
            'sigi-sincronizado',
            'sigi-novo',
            'sigi-protocolo-gerado',
            'prioridade-normal',
            'setor-atendimento-geral',
        ];

        if (self::CHANNEL_WHATSAPP === $channel) {
            $labels = array_merge($labels, [
                'origem-whatsapp',
                'canal-whatsapp',
                'sigi-whatsapp-validado',
                'contato-telefone-identificado',
            ]);

            if ($customerProtocolMessageSent) {
                $labels[] = 'protocolo-enviado-whatsapp';
            }
        }

        if (self::CHANNEL_INSTAGRAM === $channel) {
            $labels = array_merge($labels, [
                'origem-instagram',
                'canal-instagram',
                'sigi-instagram-validado',
                'contato-social-identificado',
            ]);

            if ($customerProtocolMessageSent) {
                $labels[] = 'protocolo-enviado-instagram';
            }
        }

        if (self::CHANNEL_EMAIL === $channel) {
            $labels = array_merge($labels, [
                'origem-email',
                'canal-email',
            ]);
        }

        return array_values(array_unique($labels));
    }

    public function protocolMessage(string $channel, string $fallbackTemplate): string
    {
        return match ($channel) {
            self::CHANNEL_WHATSAPP => 'Ola! Recebemos sua solicitacao no atendimento do Sertao Digital. Seu numero de protocolo e: {protocol}. Nossa equipe analisara sua mensagem e retornara em breve.',
            self::CHANNEL_INSTAGRAM => 'Ola! Recebemos sua mensagem no atendimento do Sertao Digital. Seu numero de protocolo e: {protocol}. Nossa equipe analisara sua solicitacao e retornara por aqui.',
            default => $fallbackTemplate,
        };
    }
}
