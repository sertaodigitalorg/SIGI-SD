<?php

namespace App\Controller\Admin;

use App\Entity\OrganizationContact;
use App\Form\OrganizationContactType;
use App\Repository\OrganizationContactRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/organization-contact', name: 'admin_organization_contact_')]
#[IsGranted('ROLE_ADMIN')]
final class OrganizationContactController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(OrganizationContactRepository $repository): Response
    {
        return $this->render('admin/organization_contact/index.html.twig', [
            'contacts' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new OrganizationContact();
        $form = $this->createForm(OrganizationContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Contato institucional criado com sucesso.');

            return $this->redirectToRoute('admin_organization_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/organization_contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(OrganizationContact $contact): Response
    {
        return $this->render('admin/organization_contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OrganizationContact $contact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrganizationContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Contato institucional atualizado com sucesso.');

            return $this->redirectToRoute('admin_organization_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/organization_contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, OrganizationContact $contact, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$contact->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Não foi possível validar a solicitação de exclusão.');

            return $this->redirectToRoute('admin_organization_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        try {
            $entityManager->remove($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Contato institucional removido com sucesso.');
        } catch (ForeignKeyConstraintViolationException) {
            $this->addFlash('danger', 'O contato não pode ser removido porque possui vínculos ativos no sistema.');
        }

        return $this->redirectToRoute('admin_organization_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}