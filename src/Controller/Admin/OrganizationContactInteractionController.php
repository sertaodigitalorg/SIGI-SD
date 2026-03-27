<?php

namespace App\Controller\Admin;

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

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(OrganizationContactInteraction $interaction): Response
    {
        return $this->render('admin/organization_contact_interaction/show.html.twig', [
            'interaction' => $interaction,
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
