<?php

namespace App\Controller\Admin;

use App\Entity\Optione;
use App\Form\OptioneType;
use App\Repository\OptioneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/optione")
 */
class AdminOptioneController extends AbstractController
{
    /**
     * @Route("/", name="admin.optione.index", methods={"GET"})
     * @param OptioneRepository $optioneRepository
     * @return Response
     */
    public function index(OptioneRepository $optioneRepository): Response
    {
        return $this->render('admin/optione/index.html.twig', [
            'optiones' => $optioneRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin.optione.new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $optione = new Optione();
        $form = $this->createForm(OptioneType::class, $optione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($optione);
            $entityManager->flush();

            return $this->redirectToRoute('admin.optione.index');
        }

        return $this->render('admin/optione/new.html.twig', [
            'optione' => $optione,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin.optione.edit", methods={"GET","POST"})
     * @param Request $request
     * @param Optione $optione
     * @return Response
     */
    public function edit(Request $request, Optione $optione): Response
    {
        $form = $this->createForm(OptioneType::class, $optione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin.optione.index');
        }

        return $this->render('admin/optione/edit.html.twig', [
            'optione' => $optione,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin.optione.delete", methods={"DELETE"})
     * @param Request $request
     * @param Optione $optione
     * @return Response
     */
    public function delete(Request $request, Optione $optione): Response
    {
        if ($this->isCsrfTokenValid('admin/delete'.$optione->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($optione);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.optione.index');
    }
}
