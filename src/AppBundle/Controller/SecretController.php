<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Secret;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;

class SecretController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction(Request $request)
    {
        if ($request->get('add')) {
            $dateTime = new \DateTime();

            $secret = new Secret();
            $secret
                ->setSecret('stephen')
                ->setViews(5)
                ->setExpires($dateTime->add(new \DateInterval('P3D')));

            $this->getRepository()->save($secret);
        }

        $data = $this->getRepository()->findAll();

        $view = $this->view($data, 200)
            ->setTemplate(':secrets:cget_action.html.twig')
            ->setTemplateVar('secrets');

        return $this->handleView($view);
    }

    public function newAction()
    {
    }

    public function getAction($uuid)
    {
        // fae81dfb-b88b-41a5-bf95-46518791d341
        $data = $this->getRepository()->find(strtoupper($uuid));

        $view = $this->view($data, 200)
            ->setTemplate(':secrets:get_action.html.twig')
            ->setTemplateVar('secret');

        return $this->handleView($view);
    }

    /**
     * @return \AppBundle\Repository\SecretRepository
     */
    private function getRepository()
    {
        return $this->container->get('app.secret.repository');
    }
}
