<?php

namespace App\Controller\Admin;

use App\Entity\InteractionThread;
use App\Entity\PersonContactInteraction;
use App\Form\PersonContactInteractionType;
use App\Repository\PersonContactInteractionRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/person-contact-interaction', name: 'admin_person_contact_interaction_')]
#[IsGranted('ROLE_ADMIN')]
final class PersonContactInteractionController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PersonContactInteractionRepository $repository): Response
    {
        $interactions = $repository->createQueryBuilder('i')
            ->join('i.personContact', 'pc')
            ->join('pc.person', 'p')
            ->leftJoin('i.interactionStatus', 's')
            ->leftJoin('i.performedBy', 'u')
            ->addSelect('pc')
            ->addSelect('p')
            ->addSelect('s')
            ->addSelect('u')
            ->orderBy('i.contactedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/person_contact_interaction/index.html.twig', [
            'interactions' => $interactions,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $interaction = new PersonContactInteraction();
        $interaction->setContactedAt(new \DateTimeImmutable());

        $form = $this->createForm(PersonContactInteractionType::class, $interaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$interaction->getPerformedBy() && $this->getUser()) {
                $interaction->setPerformedBy($this->getUser());
            }

            $entityManager->persist($interaction);
            $entityManager->flush();

            $this->addFlash('success', 'Interação com pessoa criada com sucesso.');

            return $this->redirectToRoute('admin_person_contact_interaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/person_contact_interaction/new.html.twig', [
            'interaction' => $interaction,
            'form' => $form,
        ]);
    }

    #[Route('/person/{id}/contacts', name: 'person_contact_interaction_contacts', methods: ['GET'])]
    public function personContacts(int $id, \App\Repository\PersonContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findBy(
            ['person' => $id],
            ['contactType' => 'ASC', 'value' => 'ASC']
        );

        $payload = array_map(fn(\App\Entity\PersonContact $contact) => [
            'id' => $contact->getId(),
            'label' => sprintf('%s — %s', $contact->getContactType()?->getName() ?: '-', $contact->getValue() ?: '-'),
        ], $contacts);

        return $this->json($payload);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(PersonContactInteraction $interaction, PersonContactInteractionRepository $repository): Response
    {
        $interactionsHistory = [];

        if ($interaction->getThread()) {
            $interactionsHistory = $repository->findBy(
                ['thread' => $interaction->getThread()],
                ['contactedAt' => 'ASC']
            );
        }

        return $this->render('admin/person_contact_interaction/show.html.twig', [
            'interaction' => $interaction,
            'interactionsHistory' => $interactionsHistory,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PersonContactInteraction $interaction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonContactInteractionType::class, $interaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$interaction->getPerformedBy() && $this->getUser()) {
                $interaction->setPerformedBy($this->getUser());
            }

            $entityManager->flush();

            $this->addFlash('success', 'Interação com pessoa atualizada com sucesso.');

            return $this->redirectToRoute('admin_person_contact_interaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/person_contact_interaction/edit.html.twig', [
            'interaction' => $interaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/respond', name: 'respond', methods: ['GET', 'POST'])]
    public function respond(Request $request, PersonContactInteraction $interaction, EntityManagerInterface $entityManager): Response
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

        $newInteraction = new PersonContactInteraction();
        $newInteraction->setPersonContact($interaction->getPersonContact());
        $newInteraction->setThread($thread);
        $newInteraction->setSubject($interaction->getSubject());
        $newInteraction->setNextContactAt($interaction->getNextContactAt());
        $newInteraction->setInteractionStatus($interaction->getInteractionStatus());
        $newInteraction->setContactedAt(new \DateTimeImmutable());

        $form = $this->createForm(PersonContactInteractionType::class, $newInteraction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$newInteraction->getPerformedBy() && $this->getUser()) {
                $newInteraction->setPerformedBy($this->getUser());
            }

            $entityManager->persist($newInteraction);
            $entityManager->flush();

            $this->addFlash('success', 'Resposta cadastrada com sucesso.');

            return $this->redirectToRoute('admin_person_contact_interaction_show', ['id' => $newInteraction->getId()]);
        }

        return $this->render('admin/person_contact_interaction/respond.html.twig', [
            'interaction' => $interaction,
            'form' => $form,
            'thread' => $thread,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, PersonContactInteraction $interaction, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$interaction->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Não foi possível validar a solicitação de exclusão.');

            return $this->redirectToRoute('admin_person_contact_interaction_index', [], Response::HTTP_SEE_OTHER);
        }

        try {
            $entityManager->remove($interaction);
            $entityManager->flush();

            $this->addFlash('success', 'Interação com pessoa removida com sucesso.');
        } catch (ForeignKeyConstraintViolationException) {
            $this->addFlash('danger', 'A interação não pode ser removida porque possui vínculos ativos no sistema.');
        }

        return $this->redirectToRoute('admin_person_contact_interaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
