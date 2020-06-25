<?php

namespace App\Controller;

use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Utils\ImageFile;
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
     *
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
        $logo = $project->getLogo();
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
            $file = $form->get('logo')->getData();
            $path = $this->getParameter('pathProjectLogo');
            if ($file) {
                $width = $this->getParameter('projectLogoWidth');
                $height = $this->getParameter('projectLogoHeight');
                $fileName = ImageFile::upload($file, $path);
                if ($logo) {
                    ImageFile::deleteFile($path . '/' . $logo);
                }
                ImageFile::createThumb("{$path}/{$fileName}", $width, $height);
                $project->setLogo($fileName);
            } elseif ($logo) {
                $deleteLogo = $request->get('delete_logo');
                if ($deleteLogo) {
                    ImageFile::deleteFile("{$path}/{$logo}");
                } else {
                    $project->setLogo($logo);
                }
            }
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request)
    {
        $id = $request->get('id');
        $project = $this->projectRepository->find($id);
        if ($project) {
            $userId = $this->getUser()->getId();
            if ($project->getOwnerId() === $userId) {
                $this->projectRepository->delete($project);
                return $this->redirectToRoute('user_project_list');
            }
        }

    }
}