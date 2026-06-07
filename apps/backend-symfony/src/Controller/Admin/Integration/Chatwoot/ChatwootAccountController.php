<?php

namespace App\Controller\Admin\Integration\Chatwoot;

use App\Entity\Integration\Chatwoot\ChatwootAccount;
use App\Entity\User;
use App\Form\ChatwootAccountType;
use App\Repository\Integration\Chatwoot\ChatwootAccountRepository;
use App\Service\Integration\Chatwoot\ChatwootApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/integrations/chatwoot/accounts'), IsGranted(User::ROLE_ADMIN)]
final class ChatwootAccountController extends AbstractController
{
    #[Route('', name: 'admin_chatwoot_account_index', methods: ['GET'])]
    public function index(ChatwootAccountRepository $accountRepository): Response
    {
        return $this->render('admin/integration/chatwoot/accounts/index.html.twig', [
            'accounts' => $accountRepository->createAdminListQueryBuilder()->getQuery()->getResult(),
        ]);
    }

    #[Route('/new', name: 'admin_chatwoot_account_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $account = new ChatwootAccount();
        $form = $this->createForm(ChatwootAccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account->touch();
            $entityManager->persist($account);
            $entityManager->flush();

            $this->addFlash('success', 'Conta Chatwoot criada com sucesso.');

            return $this->redirectToRoute('admin_chatwoot_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/integration/chatwoot/accounts/new.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_chatwoot_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ChatwootAccount $account, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChatwootAccountType::class, $account, [
            'require_secrets' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiToken = $form->get('apiToken')->getData();
            if (is_string($apiToken) && '' !== trim($apiToken)) {
                $account->setApiToken($apiToken);
            }

            $webhookSecret = $form->get('webhookSecret')->getData();
            if (is_string($webhookSecret) && '' !== trim($webhookSecret)) {
                $account->setWebhookSecret($webhookSecret);
            }

            $account->touch();
            $entityManager->flush();

            $this->addFlash('success', 'Conta Chatwoot atualizada com sucesso.');

            return $this->redirectToRoute('admin_chatwoot_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/integration/chatwoot/accounts/edit.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/test', name: 'admin_chatwoot_account_test', methods: ['POST'])]
    public function test(Request $request, ChatwootAccount $account, ChatwootApiClient $apiClient): Response
    {
        if (!$this->isCsrfTokenValid('test_chatwoot_account'.$account->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Nao foi possivel validar a solicitacao.');

            return $this->redirectToRoute('admin_chatwoot_account_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($apiClient->testConnection($account)) {
            $this->addFlash('success', 'Conexao com Chatwoot validada com sucesso.');
        } else {
            $this->addFlash('danger', 'Nao foi possivel validar a conexao com Chatwoot.');
        }

        return $this->redirectToRoute('admin_chatwoot_account_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle', name: 'admin_chatwoot_account_toggle', methods: ['POST'])]
    public function toggle(Request $request, ChatwootAccount $account, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('toggle_chatwoot_account'.$account->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Nao foi possivel validar a solicitacao.');

            return $this->redirectToRoute('admin_chatwoot_account_index', [], Response::HTTP_SEE_OTHER);
        }

        $account->setIsActive(!$account->isActive());
        $account->touch();
        $entityManager->flush();

        $this->addFlash('success', $account->isActive() ? 'Conta Chatwoot ativada.' : 'Conta Chatwoot desativada.');

        return $this->redirectToRoute('admin_chatwoot_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
