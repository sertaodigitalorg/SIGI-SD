<?php

namespace App\Form;

use App\Entity\Integration\Chatwoot\ChatwootAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChatwootAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $requireSecrets = (bool) $options['require_secrets'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nome',
            ])
            ->add('baseUrl', UrlType::class, [
                'label' => 'URL base',
                'help' => 'Exemplo: https://chat.sigi.localhost',
            ])
            ->add('accountId', TextType::class, [
                'label' => 'ID da conta no Chatwoot',
                'required' => false,
                'help' => 'Numero que aparece na URL do Chatwoot: /app/accounts/{id}.',
            ])
            ->add('inboxId', TextType::class, [
                'label' => 'Inbox ID opcional',
                'required' => false,
                'help' => 'Use apenas se quiser limitar a sincronizacao a uma inbox.',
            ])
            ->add('apiToken', PasswordType::class, [
                'label' => 'API token',
                'required' => $requireSecrets,
                'mapped' => $requireSecrets,
                'always_empty' => true,
                'help' => $requireSecrets ? 'Token usado nas chamadas futuras para a API do Chatwoot.' : 'Preencha apenas se desejar substituir o token salvo.',
            ])
            ->add('webhookSecret', PasswordType::class, [
                'label' => 'Webhook secret',
                'required' => $requireSecrets,
                'mapped' => $requireSecrets,
                'always_empty' => true,
                'help' => $requireSecrets ? 'Secret que sera enviado no header do webhook.' : 'Preencha apenas se desejar substituir o secret salvo.',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Ativo',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChatwootAccount::class,
            'require_secrets' => true,
        ]);

        $resolver->setAllowedTypes('require_secrets', 'bool');
    }
}
