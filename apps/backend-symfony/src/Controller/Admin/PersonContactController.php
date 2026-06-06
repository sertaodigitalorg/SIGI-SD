<?php

namespace App\Controller\Admin;

use App\Entity\PersonContact;
use App\Form\PersonContactType;
use App\Repository\PersonContactRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/person-contact', name: 'admin_person_contact_')]
#[IsGranted('ROLE_ADMIN')]
final class PersonContactController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PersonContactRepository $repository): Response
    {
        $contacts = $repository->createQueryBuilder('pc')
            ->join('pc.person', 'p')
            ->leftJoin('pc.contactType', 'ct')
            ->addSelect('p')
            ->addSelect('ct')
            ->orderBy('p.fullName', 'ASC')
            ->addOrderBy('ct.name', 'ASC')
            ->addOrderBy('pc.value', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/person_contact/index.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new PersonContact();
        $form = $this->createForm(PersonContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Contato da pessoa criado com sucesso.');

            return $this->redirectToRoute('admin_person_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/person_contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(PersonContact $contact): Response
    {
        return $this->render('admin/person_contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PersonContact $contact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Contato da pessoa atualizado com sucesso.');

            return $this->redirectToRoute('admin_person_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/person_contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, PersonContact $contact, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$contact->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Não foi possível validar a solicitação de exclusão.');

            return $this->redirectToRoute('admin_person_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        try {
            $entityManager->remove($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Contato da pessoa removido com sucesso.');
        } catch (ForeignKeyConstraintViolationException) {
            $this->addFlash('danger', 'O contato não pode ser removido porque possui vínculos ativos no sistema.');
        }

        return $this->redirectToRoute('admin_person_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
