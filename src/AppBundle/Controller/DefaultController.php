<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/api/{path}", name="api_docs", requirements={"path"=".+"})
     */
    public function fileAction($path)
    {
        $repository = $this->container->get('documentation_file.repository');
        $data = $repository->findOneBy(array('name' => $path));

        $response = $this->render('default/file.twig', array('data' => $data->getFile()->getBytes()));
        $response->headers->set('Content-Type', $data->getMimeType());
        return $response;
    }

    /**
     * @Route("/{owner}/{project}", name="project")
     */
    public function projectAction($owner, $project)
    {
        $repository = $this->container->get('project.repository');
        $project = $repository->findOneBy(array('name' => $project));
        $versions = array();
        foreach ($project->getVersions() as $version) {
            $versions[] = array(
                'name' => $version, 
                'href' => $this->container->get('router')->generate('api_docs', array(
                    'path' => sprintf('%s/%s/%s/index.html', $project->getOwner()->getUsername(), $project->getName(), $version)
                ))
            );
        }
        return $this->render('default/project.html.twig', array(
            'project' => array(
                'name' => $project->getName(),
                'masterVersion' => $project->getMasterVersion(),
                'owner' => array(
                    'name' => $project->getOwner()->getUsername(),
                    'href' => $this->container->get('router')->generate('homepage')
                )
            ),
            'versions' => $versions
        ));
     }
}
