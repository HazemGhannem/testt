<?php

namespace App\Controller;

use App\Entity\Chef;
use App\Form\ChefType;
use App\Repository\ChefRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chef")
 */
class ChefController extends AbstractController
{
    /**
     * @Route("/", name="chef_index", methods={"GET"})
     */
    public function index(ChefRepository $chefRepository): Response
    {
        return $this->render('chef/index.html.twig', [
            'chefs' => $chefRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="chef_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chef = new Chef();
        $form = $this->createForm(ChefType::class, $chef);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chef);
            $entityManager->flush();

            return $this->redirectToRoute('chef_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chef/new.html.twig', [
            'chef' => $chef,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="chef_show", methods={"GET"})
     */
    public function show(Chef $chef): Response
    {
        return $this->render('chef/show.html.twig', [
            'chef' => $chef,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="chef_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Chef $chef, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChefType::class, $chef);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('chef_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chef/edit.html.twig', [
            'chef' => $chef,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="chef_delete", methods={"POST"})
     */
    public function delete(Request $request, Chef $chef, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chef->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chef);
            $entityManager->flush();
        }

        return $this->redirectToRoute('chef_index', [], Response::HTTP_SEE_OTHER);
    }
}
