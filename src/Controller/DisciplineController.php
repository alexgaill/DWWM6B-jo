<?php

namespace App\Controller;

use App\Entity\Discipline;
use App\Form\DisciplineType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisciplineController extends AbstractController
{
    #[Route('/discipline', name: 'app_discipline', methods:["GET", "POST"])]
    public function index(Request $request, ManagerRegistry $manager): Response
    {
        $discipline = new Discipline;
        $form = $this->createForm(DisciplineType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $manager->getManager();
            $om->persist($discipline);
            $om->flush();

            return $this->redirectToRoute('app_discipline');
        }

        return $this->renderForm('discipline/index.html.twig', [
            'disciplines' => $manager->getRepository(Discipline::class)->findAll(),
            'form' => $form
        ]);
    }
    
    #[Route("/discipline/{id}/update", name:"update_discipline", methods:["GET", "POST"], requirements:['id' => "\d+"])]
    public function update (int $id, Request $request, MAnagerRegistry $manager): Response
    {
        $discipline = $manager->getRepository(Discipline::class)->find($id);

        if (!$discipline) {
            return $this->redirectToRoute('app_discipline');
        }

        $form = $this->createForm(DisciplineType::class, $discipline);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $om = $manager->getManager();
            $om->persist($discipline);
            $om->flush();

            return $this->redirectToRoute('app_discipline');
        }

        return $this->renderForm("discipline/update.html.twig", [
            'form' => $form
        ]);
    }

    #[Route("/discipline/{id}/delete", name:"delete_discipline", methods:['GET'], requirements:['id' => "\d+"])]
    public function delete (int $id, ManagerRegistry $manager): Response
    {
        $discipline = $manager->getRepository(Discipline::class)->find($id);

        if ($discipline) {
            $om = $manager->getManager();
            $om->remove($discipline);
            $om->flush();
        }

        return $this->redirectToRoute('app_discipline');
    }

}
