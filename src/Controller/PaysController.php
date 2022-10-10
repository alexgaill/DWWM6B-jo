<?php

namespace App\Controller;

use App\Entity\Pays;
use App\Form\PaysType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Annotation\Route;

class PaysController extends AbstractController
{
    #[Route('/pays', name: 'app_pays', methods:['GET', 'POST'])]
    public function index(Request $request, ManagerRegistry $manager): Response
    {
        $pays = new Pays;
        $form = $this->createForm(PaysType::class, $pays);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $drapeau = $form->get('drapeau')->getData();

            $drapeauName = md5(uniqid()). '.' . $drapeau->guessExtension();

            $drapeau->move(
                $this->getParameter('upload_drapeau'),
                $drapeauName
            );

            $pays->setDrapeau($drapeauName);
            $om = $manager->getManager();
            $om->persist($pays);
            $om->flush();

            return $this->redirectToRoute('app_pays');
        }

        return $this->renderForm('pays/index.html.twig', [
            'paysListe' => $manager->getRepository(Pays::class)->findAll(),
            'form' => $form
        ]);
    }

    #[Route("pays/{id}/update", name:"update_pays", methods:['GET', 'POST'], requirements:['id' => "\d+"])]
    public function update (int $id, Request $request, ManagerRegistry $manager): Response
    {
        $pays = $manager->getRepository(Pays::class)->find($id);
        if (!$pays) {
            return $this->redirectToRoute('app_pays');
        }

        $form = $this->createForm(PaysType::class, $pays);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $drapeau = $form->get('drapeau')->getData();

            if ($drapeau) {
                $oldDrapeau = $pays->getDrapeau();
                if ($oldDrapeau) {
                    unlink($this->getParameter('upload_drapeau') . '/' . $oldDrapeau);
                }
                $drapeauName = md5(uniqid()). '.' . $drapeau->guessExtension();

                $drapeau->move(
                    $this->getParameter('upload_drapeau'),
                    $drapeauName
                );
                $pays->setDrapeau($drapeauName);
            }
            
            $om = $manager->getManager();
            $om->persist($pays);
            $om->flush();

            return $this->redirectToRoute('app_pays');
        }

        return $this->renderForm('pays/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("pays/{id}/delete", name:"delete_pays", methods:['GET', 'POST'], requirements:['id' => "\d+"])]
    public function delete (int $id, ManagerRegistry $manager): Response
    {
        $pays = $manager->getRepository(Pays::class)->find($id);
        if ($pays) {
            $om = $manager->getManager();
            $om->remove($pays);
            $om->flush();
        }

        return $this->redirectToRoute("app_pays");
    }
}
