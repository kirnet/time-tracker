<?php

namespace App\Controller;


use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    /** @var ProjectRepository */
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/edit/{id}", name="project_edit",  defaults={"id"=0})
     * @param Request $request
     *
     * @return string|JsonResponse|RedirectResponse|Response
     * @throws \Exception
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');
        $isAjax = $request->isXmlHttpRequest();
        if ($id) {
            $project = $this->projectRepository->find($id);
        } else {
            $project = new Project();
            $project->setUser($this->getUser());
//            $project->setCreatedAt(new \DateTime());
        }
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            if ($isAjax) {
                return new JsonResponse($project);
            }
            return $this->redirectToRoute('user_project_list');

        }
        $viewData = [
            'isAjax' => $isAjax,
            'form' => $form->createView()
        ];
        if ($isAjax) {
            return $this->renderView('project/_form.html.twig', $viewData);
        }
        return $this->render('project/edit.html.twig', $viewData);
    }

    /**
     * @Route("/delete/{id}", name="project_delete")
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function delete(Request $request)
    {
        $id = $request->get('id');
        $project = $this->projectRepository->find($id);
        if ($project) {
            $userId = $this->getUser()->getId();
            if ($project->getUser()->getId() === $userId) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($project);
                $em->flush();
                return $this->redirectToRoute('user_project_list');
            }
        }

    }
}