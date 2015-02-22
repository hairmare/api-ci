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
        $repository = $this->container->get('testimonial.repository');
        $testimonials = array();
        foreach ($repository->findBy(array(), null, 3) as $testimonial) {
            $testimonials[] = array('text' => $testimonial->getText(), 'author' => $testimonial->getAuthor());
        }
        return $this->render('default/index.html.twig', array(
            'testimonials' => $testimonials,
        ));
    }

    /**
     * @Route("/about", name="about")
     */
    public function aboutAction()
    {
        return $this->render('default/about.html.twig');
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
     * @Route("/projects", name="projects")
     */
    public function projectsAction()
    {
        $repository = $this->container->get('project.repository');
        $projects = array();
        foreach ($repository->getRecentProjects(50) as $project) {
            $projects[] = array(
                'name' => $project->getName(),
                'href' => $this->container->get('router')->generate('project', array(
                    'owner' => $project->getOwner()->getUsername(),
                    'project' => $project->getName()
                ))
            );
        }
        return $this->render('default/projects.html.twig', array('projects' => $projects));
    }

    /**
     * @Route("/{owner}", name="owner")
     */
    public function ownerAction($owner)
    {
        return $this->render('default/owner.html.twig');
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
                    'href' => $this->container->get('router')->generate('owner', array('owner' => $project->getOwner()->getUsername()))
                )
            ),
            'versions' => $versions
        ));
     }
}
