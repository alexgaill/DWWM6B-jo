<?php

namespace App\Controller;

use App\Entity\Athlete;
use App\Form\AthleteType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AthleteController extends AbstractController
{
    private const REDIRECTION = 'app_athlete';
    private const PARAMETER = 'upload_profil';

    #[Route('/', name: 'app_athlete', methods:['GET', 'POST'])]
    public function index(Request $request, ManagerRegistry $manager): Response
    {
        $athlete = new Athlete;
        $form = $this->createForm(AthleteType::class, $athlete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            $photoName = md5(uniqid()). '.' . $photo->guessExtension();

            $photo->move(
                $this->getParameter(self::PARAMETER),
                $photoName
            );

            $athlete->setPhoto($photoName);

            $om = $manager->getManager();
            $om->persist($athlete);
            $om->flush();

            return $this->redirectToRoute(self::REDIRECTION);
        }
        return $this->renderForm('athlete/index.html.twig', [
            'athletes' => $manager->getRepository(Athlete::class)->findAll(),
            'form' => $form
        ]);
    }

    #[Route("/athlete/{id}/update", name:"update_athlete", methods:["GET", "POST"], requirements:['id' => "\d+"])]
    public function update (int $id, ManagerRegistry $manager, Request $request): Response
    {
        $athlete = $manager->getRepository(Athlete::class)->find($id);

        if (!$athlete) {
            return $this->redirectToRoute(self::REDIRECTION);
        }

        $form = $this->createForm(AthleteType::class, $athlete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();

            if ($photo) {
                $oldPhoto = $athlete->getPhoto();
                if ($oldPhoto) {
                    unlink($this->getParameter(self::PARAMETER) .'/'. $athlete->getPhoto());
                }
                $photoName = md5(uniqid()) .'.'. $photo->guessExtension();

                $photo->move(
                    $this->getParameter(self::PARAMETER),
                    $photoName
                );

                $athlete->setPhoto($photoName);
            }

            $om = $manager->getManager();
            $om->persist($athlete);
            $om->flush();

            return $this->redirectToRoute(self::REDIRECTION);
        }

        return $this->renderForm('athlete/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/athlete/{id}/delete', name: "delete_athlete", methods:["GET", "POST"], requirements:['id' => "\d+"])]
    public function delete(int $id, ManagerRegistry $manager): Response
    {
        $athlete = $manager->getRepository(Athlete::class)->find($id);
        if ($athlete) {
            $om = $manager->getManager();
            $om->remove($athlete);
            $om->flush();
        }

        return $this->redirectToRoute(self::REDIRECTION);
    }
}
