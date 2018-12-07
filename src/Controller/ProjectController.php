<?php

namespace App\Controller;

use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use http\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    /**
     * ProjectController constructor.
     *
     * @param ProjectRepository $projectRepository
     */
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/edit/{id<\d+>}", name="project_edit",  defaults={"id"=0})
     * @param Request $request
     *
     * @return string|JsonResponse|RedirectResponse|Response
     * @throws \Exception
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');
        $isAjax = $request->isXmlHttpRequest();
        $project = $this->projectRepository->findOneOrCreateById($id);

        if ($project->getId() === null) {
            $project->setOwnerId($this->getUser()->getId());
        } else {
            if ($project->getOwnerId() != $this->getUser()->getId()) {
                throw new NotFoundHttpException();
            }
        }
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectRepository->save($project);
            if ($isAjax) {
                return new JsonResponse($project);
            }
            return $this->redirectToRoute('user_project_list');

        }
        $viewData = [
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
            if ($project->getOwnerId()->getId() === $userId) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($project);
                $em->flush();
                return $this->redirectToRoute('user_project_list');
            }
        }

    }
}