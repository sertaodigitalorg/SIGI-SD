<?php

namespace App\Controller\Admin;

use App\Entity\Organization;
use App\Entity\User;
use App\Form\OrganizationTypeForm;
use App\Repository\OrganizationRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/organizations'), IsGranted(User::ROLE_ADMIN)]
final class OrganizationController extends AbstractController
{
    #[Route('', name: 'organization_index', methods: ['GET'])]
    public function index(OrganizationRepository $organizationRepository): Response
    {
        return $this->render('admin/organization/index.html.twig', [
            'organizations' => $organizationRepository->createAlphabeticalQueryBuilder()->getQuery()->getResult(),
        ]);
    }

    #[Route('/new', name: 'organization_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $organization = new Organization();
        $form = $this->createForm(OrganizationTypeForm::class, $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organization->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($organization);
            $entityManager->flush();

            $this->addFlash('success', 'Organização criada com sucesso.');

            return $this->redirectToRoute('organization_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/organization/new.html.twig', [
            'organization' => $organization,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'organization_show', methods: ['GET'])]
    public function show(Organization $organization): Response
    {
        return $this->render('admin/organization/show.html.twig', [
            'organization' => $organization,
        ]);
    }

    #[Route('/{id}/edit', name: 'organization_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Organization $organization, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrganizationTypeForm::class, $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $organization->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Organização atualizada com sucesso.');

            return $this->redirectToRoute('organization_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/organization/edit.html.twig', [
            'organization' => $organization,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'organization_delete', methods: ['POST'])]
    public function delete(Request $request, Organization $organization, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$organization->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Não foi possível validar a solicitação de exclusão.');

            return $this->redirectToRoute('organization_index', [], Response::HTTP_SEE_OTHER);
        }

        try {
            $entityManager->remove($organization);
            $entityManager->flush();

            $this->addFlash('success', 'Organização excluída com sucesso.');
        } catch (ForeignKeyConstraintViolationException) {
            $this->addFlash('danger', 'A organização não pode ser excluída porque possui vínculos ativos no sistema.');
        }

        return $this->redirectToRoute('organization_index', [], Response::HTTP_SEE_OTHER);
    }
}