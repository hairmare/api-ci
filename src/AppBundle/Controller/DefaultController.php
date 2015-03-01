<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @Route("/d/{path}", name="api_docs", requirements={"path"=".+"})
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
                'name' => sprintf('%s/%s', $project->getOwner()->getUsername(), $project->getName()),
                'href' => $this->container->get('router')->generate('project', array(
                    'owner' => $project->getOwner()->getUsername(),
                    'project' => $project->getName()
                )),
                'updated_at' => $project->getUpdatedAt()
            );
        }
        return $this->render('default/projects.html.twig', array('projects' => $projects));
    }

    /**
     * @Route("/u/{owner}", name="owner")
     */
    public function ownerAction($owner)
    {
        $repository = $this->container->get('project.repository');
        $users = $this->container
            ->get('doctrine_mongodb.odm.document_manager')
            ->createQueryBuilder('Application\Sonata\UserBundle\Document\User')
            ->field('username')->equals($owner)
            ->getQuery()
            ->execute();
        foreach ($users as $u) {
            $user = $u;
        }

        $projects = array();
        foreach ($repository->getByUser($user) as $project) {
            $projects[] = array(
                'name' => sprintf('%s/%s', $project->getOwner()->getUsername(), $project->getName()),
                'href' => $this->container->get('router')->generate('project', array('owner' => $project->getOwner()->getUsername(), 'project' => $project->getName())),
                'updated_at' => $project->getUpdatedAt()
            );
        }
            
        return $this->render('default/owner.html.twig', array('projects' => $projects));
    }

    /**
     * @Route("/p/{owner}/{project}", name="project")
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

    /**
     * @Route("/webhook", name="webhook")
     * @Method({"POST"})
     */
    public function webhookAction()
    {
        $decoder = new JsonDecode;
        $content = $this->get("request")->getContent();

        $data = $decoder->decode($content, 'json');

        $githubName = $data->repository->full_name;

        $repository = $this->container->get('project.repository');
        $project = $repository->findOneBy(array('githubName' => $githubName));

        $project->setNeedsUpdate(true);

        $dm = $this->container->get('doctrine_mongodb.odm.document_manager');
        $dm->persist($project);
        $dm->flush();

        return new JsonResponse("OK");
    }
}
