<?php

namespace App\Controller\Admin;

use App\Entity\PersonOrganization;
use App\Entity\PersonOrganizationRole;
use App\Form\PersonOrganizationRoleType;
use App\Form\PersonOrganizationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/person-organization', name: 'admin_person_organization_')]
#[IsGranted('ROLE_ADMIN')]
final class PersonOrganizationController extends AbstractController
{
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PersonOrganization $personOrganization, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonOrganizationType::class, $personOrganization, [
            'include_organization' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$personOrganization->getStartDate()) {
                $personOrganization->setStartDate(new \DateTimeImmutable());
            }

            $entityManager->flush();
            $this->addFlash('success', 'Vínculo institucional atualizado com sucesso.');

            return $this->redirectToRoute('admin_person_show', ['id' => $personOrganization->getPerson()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/person/edit_organization.html.twig', [
            'personOrganization' => $personOrganization,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/close', name: 'close', methods: ['POST'])]
    public function close(Request $request, PersonOrganization $personOrganization, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('close_person_organization' . $personOrganization->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Não foi possível validar a solicitação de encerramento do vínculo.');

            return $this->redirectToRoute('admin_person_show', ['id' => $personOrganization->getPerson()->getId()]);
        }

        if (!$personOrganization->getEndDate()) {
            $personOrganization->setEndDate(new \DateTimeImmutable());
        }

        $personOrganization->setStatus('Encerrado');
        $entityManager->flush();

        $this->addFlash('success', 'Vínculo institucional encerrado com sucesso.');

        return $this->redirectToRoute('admin_person_show', ['id' => $personOrganization->getPerson()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, PersonOrganization $personOrganization, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_person_organization' . $personOrganization->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Não foi possível validar a solicitação de exclusão do vínculo.');

            return $this->redirectToRoute('admin_person_show', ['id' => $personOrganization->getPerson()->getId()]);
        }

        $personId = $personOrganization->getPerson()->getId();
        $entityManager->remove($personOrganization);
        $entityManager->flush();

        $this->addFlash('success', 'Vínculo institucional excluído com sucesso.');

        return $this->redirectToRoute('admin_person_show', ['id' => $personId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/roles', name: 'roles', methods: ['GET', 'POST'])]
    public function roles(Request $request, PersonOrganization $personOrganization, EntityManagerInterface $entityManager): Response
    {
        $personOrganizationRole = new PersonOrganizationRole();
        $personOrganizationRole->setPersonOrganization($personOrganization);

        $form = $this->createForm(PersonOrganizationRoleType::class, $personOrganizationRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($personOrganizationRole);
            $entityManager->flush();

            $this->addFlash('success', 'Papel adicionado ao vínculo com sucesso.');

            return $this->redirectToRoute('admin_person_organization_roles', ['id' => $personOrganization->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/person_organization/roles.html.twig', [
            'personOrganization' => $personOrganization,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/roles/{roleId}/delete', name: 'role_delete', methods: ['POST'])]
    public function deleteRole(Request $request, PersonOrganization $personOrganization, PersonOrganizationRole $roleId, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete_person_organization_role' . $roleId->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('warning', 'Não foi possível validar a solicitação de remoção do papel.');

            return $this->redirectToRoute('admin_person_organization_roles', ['id' => $personOrganization->getId()]);
        }

        if ($roleId->getPersonOrganization()?->getId() !== $personOrganization->getId()) {
            $this->addFlash('danger', 'Papel inválido para este vínculo institucional.');

            return $this->redirectToRoute('admin_person_organization_roles', ['id' => $personOrganization->getId()]);
        }

        $entityManager->remove($roleId);
        $entityManager->flush();

        $this->addFlash('success', 'Papel removido com sucesso.');

        return $this->redirectToRoute('admin_person_organization_roles', ['id' => $personOrganization->getId()], Response::HTTP_SEE_OTHER);
    }
}
