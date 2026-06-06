<?php

namespace App\Controller\Admin;

use App\Entity\InteractionThread;
use App\Entity\OrganizationContactInteraction;
use App\Form\OrganizationContactInteractionType;
use App\Repository\OrganizationContactInteractionRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/organization-contact-interaction', name: 'admin_organization_contact_interaction_')]
#[IsGranted('ROLE_ADMIN')]
final class OrganizationContactInteractionController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(OrganizationContactInteractionRepository $repository): Response
    {
        $interactions = $repository->findBy([], ['contactedAt' => 'DESC']);

        return $this->render('admin/organization_contact_interaction/index.html.twig', [
            'interactions' => $interactions,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $interaction = new OrganizationContactInteraction();
        $interaction->setContactedAt(new \DateTimeImmutable());

        $form = $this->createForm(OrganizationContactInteractionType::class, $interaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($interaction);
            $entityManager->flush();

            $this->addFlash('success', 'Interação institucional criada com sucesso.');

            return $this->redirectToRoute('admin_organization_contact_interaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/organization_contact_interaction/new.html.twig', [
            'interaction' => $interaction,
            'form' => $form,
        ]);
    }

    #[Route('/organization/{id}/contacts', name: 'organization_contact_interaction_contacts', methods: ['GET'])]
    public function organizationContacts(int $id, \App\Repository\OrganizationContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findBy(
            ['organization' => $id],
            ['contactType' => 'ASC', 'value' => 'ASC']
        );

        $payload = array_map(fn(\App\Entity\OrganizationContact $contact) => [
            'id' => $contact->getId(),
            'label' => sprintf('%s — %s', $contact->getContactType()?->getName() ?: '-', $contact->getValue() ?: '-'),
        ], $contacts);

        return $this->json($payload);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(OrganizationContactInteraction $interaction, OrganizationContactInteractionRepository $repository): Response
    {
        $interactionsHistory = [];

        if ($interaction->getThread()) {
            $interactionsHistory = $repository->findBy(
                ['thread' => $interaction->getThread()],
                ['contactedAt' => 'ASC']
            );
        }

        return $this->render('admin/organization_contact_interaction/show.html.twig', [
            'interaction' => $interaction,
            'interactionsHistory' => $interactionsHistory,
        ]);
    }

    #[Route('/{id}/respond', name: 'respond', methods: ['GET', 'POST'])]
    public function respond(Request $request, OrganizationContactInteraction $interaction, EntityManagerInterface $entityManager): Response
    {
        $thread = $interaction->getThread();

        if (!$thread) {
            $thread = new InteractionThread();
            $thread->setSubject($interaction->getSubject());
            $thread->setStatus($interaction->getInteractionStatus()?->getName());

            $entityManager->persist($thread);

            $interaction->setThread($thread);
            $entityManager->flush();
        }

        $newInteraction = new OrganizationContactInteraction();
        $newInteraction->setOrganizationContact($interaction->getOrganizationContact());
        $newInteraction->setThread($thread);
        $newInteraction->setSubject($interaction->getSubject());
        $newInteraction->setNextContactAt($interaction->getNextContactAt());
        $newInteraction->setInteractionStatus($interaction->getInteractionStatus());
        $newInteraction->setContactedAt(new \DateTimeImmutable());

        $form = $this->createForm(OrganizationContactInteractionType::class, $newInteraction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$newInteraction->getPerformedBy() && $this->getUser()) {
                $newInteraction->setPerformedBy($this->getUser());
            }

            $entityManager->persist($newInteraction);
            $entityManager->flush();

            $this->addFlash('success', 'Resposta cadastrada com sucesso.');

            return $this->redirectToRoute('admin_organization_contact_interaction_show', ['id' => $newInteraction->getId()]);
        }

        return $this->render('admin/organization_contact_interaction/respond.html.twig', [
            'interaction' => $interaction,
            'form' => $form,
            'thread' => $thread,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OrganizationContactInteraction $interaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrganizationContactInteractionType::class, $interaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Interação institucional atualizada com sucesso.');

            return $this->redirectToRoute('admin_organization_contact_interaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/organization_contact_interaction/edit.html.twig', [
            'interaction' => $interaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, OrganizationContactInteraction $interaction, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$interaction->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Não foi possível validar a solicitação de exclusão.');

            return $this->redirectToRoute('admin_organization_contact_interaction_index', [], Response::HTTP_SEE_OTHER);
        }

        try {
            $entityManager->remove($interaction);
            $entityManager->flush();

            $this->addFlash('success', 'Interação institucional removida com sucesso.');
        } catch (ForeignKeyConstraintViolationException) {
            $this->addFlash('danger', 'A interação não pode ser removida porque possui vínculos ativos no sistema.');
        }

        return $this->redirectToRoute('admin_organization_contact_interaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
