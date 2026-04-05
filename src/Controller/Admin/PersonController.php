<?php

namespace App\Controller\Admin;

use App\Entity\Person;
use App\Entity\PersonOrganization;
use App\Entity\PersonOrganizationRole;
use App\Form\PersonOrganizationType;
use App\Form\PersonType;
use App\Repository\PersonOrganizationRepository;
use App\Repository\PersonRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/person', name: 'admin_person_')]
#[IsGranted('ROLE_ADMIN')]
final class PersonController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PersonRepository $personRepository): Response
    {
        return $this->render('admin/person/index.html.twig', [
            'people' => $personRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Custom validation: if role is provided but organization is not
            $organization = $form->get('organization')->getData();
            $role = $form->get('role')->getData();
            if ($role && !$organization) {
                $form->get('role')->addError(new \Symfony\Component\Form\FormError('Selecione uma organização para definir o papel.'));
                return $this->render('admin/person/new.html.twig', [
                    'person' => $person,
                    'form' => $form,
                ]);
            }

            $person->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($person);

            // Handle optional organization link
            if ($organization) {
                $startDate = $form->get('startDate')->getData();
                $status = $form->get('status')->getData();

                $personOrganization = new PersonOrganization();
                $personOrganization->setPerson($person);
                $personOrganization->setOrganization($organization);
                if ($startDate) {
                    $personOrganization->setStartDate(\DateTimeImmutable::createFromMutable($startDate));
                }
                if ($status) {
                    $personOrganization->setStatus($status);
                }

                $entityManager->persist($personOrganization);

                // Handle optional role
                if ($role) {
                    $personRole = new PersonOrganizationRole();
                    $personRole->setPersonOrganization($personOrganization);
                    $personRole->setRole($role);

                    $entityManager->persist($personRole);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Pessoa criada com sucesso.');

            return $this->redirectToRoute('admin_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/person/new.html.twig', [
            'person' => $person,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Person $person, PersonOrganizationRepository $personOrganizationRepository): Response
    {
        $personOrganizations = $personOrganizationRepository->findByPersonWithRelations($person);

        return $this->render('admin/person/show.html.twig', [
            'person' => $person,
            'personOrganizations' => $personOrganizations,
        ]);
    }

    #[Route('/{id}/add-organization', name: 'add_organization', methods: ['GET', 'POST'])]
    public function addOrganization(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        $personOrganization = new PersonOrganization();
        $personOrganization->setPerson($person);

        $form = $this->createForm(PersonOrganizationType::class, $personOrganization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$personOrganization->getStartDate()) {
                $personOrganization->setStartDate(new \DateTimeImmutable());
            }

            $entityManager->persist($personOrganization);
            $entityManager->flush();

            $this->addFlash('success', 'Vínculo institucional adicionado com sucesso.');

            return $this->redirectToRoute('admin_person_show', ['id' => $person->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/person/add_organization.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $person->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Pessoa atualizada com sucesso.');

            return $this->redirectToRoute('admin_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/person/edit.html.twig', [
            'person' => $person,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$person->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Não foi possível validar a solicitação de exclusão.');

            return $this->redirectToRoute('admin_person_index', [], Response::HTTP_SEE_OTHER);
        }

        try {
            $entityManager->remove($person);
            $entityManager->flush();

            $this->addFlash('success', 'Pessoa removida com sucesso.');
        } catch (ForeignKeyConstraintViolationException) {
            $this->addFlash('danger', 'A pessoa não pode ser removida porque possui vínculos ativos no sistema.');
        }

        return $this->redirectToRoute('admin_person_index', [], Response::HTTP_SEE_OTHER);
    }
}