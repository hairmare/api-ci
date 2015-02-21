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
}
