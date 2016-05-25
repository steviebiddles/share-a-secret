<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Secret;
use AppBundle\Form\Type\SecretType;

use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\RouteRedirectView;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

use Carbon\Carbon;

/**
 * Class SecretController
 *
 * @package AppBundle\Controller
 */
class SecretController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cgetAction()
    {
        return $this->render(':secrets:index.html.twig', array());
    }

    /**
     * Get a single secret and update view count.
     *
     * @ApiDoc(
     *   output = "AppBundle\Entity\Secret",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the secret is not found"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id the universally unique identifier
     *
     * @return array
     */
    public function getAction(Request $request, $id)
    {
        $newSecret = true;
        $secret = $this->getRepository()->findActiveSecret($id);

        if (null === $secret) {
            throw $this->createNotFoundException('Secret does not exist.');
        }

        if ($request->headers->get('referer') !==
            $this->generateUrl('new_secret', array(), UrlGenerator::ABSOLUTE_URL)
        ) {
            $newSecret = false;
            $secret->setViews($secret->getViews() - 1);

            $this->getRepository()->save($secret);
        }

        $date = Carbon::createFromFormat('Y-m-d H:i:s', $secret->getExpires()->format('Y-m-d H:i:s'));
        $dateExtra = null;

        if ($date->diffInDays() > 0) {
            $dateExtra = ' ' . $date->diff(Carbon::now(), true)->format('%h hours');
        }

        $view = $this->view($secret, Codes::HTTP_OK)
            ->setTemplate(':secrets:get.html.twig')
            ->setTemplateVar('secret')
            ->setTemplateData(array(
                'new_secret' => $newSecret,
                'date_for_humans' => $date->diffForHumans(Carbon::now(), true) . $dateExtra
            ));

        return $this->handleView($view);
    }

    /**
     * Presents the form to use to create a new secret.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $view = $this->view($this->createForm(SecretType::class, null, array(
            'action' => $this->generateUrl('post_secret')
        )), Codes::HTTP_OK)
            ->setTemplate(':secrets:new.html.twig')
            ->setTemplateVar('form');

        return $this->handleView($view);
    }

    /**
     * Creates a new secret from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "AppBundle\Form\Type\SecretType",
     *   statusCodes = {
     *     201 = "Returned when created",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(SecretType::class, new Secret());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Secret $secret */
            $secret = $form->getData();

            $this->getRepository()->save($secret);

            return $this->handleView(
                $this->routeRedirectView('get_secret', array('id' => $secret->getId()))
            );
        }

        $view = $this->view($form, Codes::HTTP_BAD_REQUEST)
            ->setTemplate(':secrets:new.html.twig')
            ->setTemplateVar('form');

        return $this->handleView($view);
    }

    /**
     * Removes a secret.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param string $id the secret uuid
     *
     * @return RouteRedirectView
     */
    public function deleteAction($id)
    {
        $secret = $this->getRepository()->find(strtoupper($id));

        $this->getRepository()->getEntityManager()->remove($secret);
        $this->getRepository()->getEntityManager()->flush();

        return $this->handleView(
            $this->routeRedirectView('get_secrets', array('p' => 1), Codes::HTTP_NO_CONTENT)
        );
    }

    /**
     * Removes a secret.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param string $id the secret uuid
     *
     * @return RouteRedirectView
     */
    public function removeAction($id)
    {
        return $this->deleteAction($id);
    }

    /**
     * @return \AppBundle\Repository\SecretRepository
     */
    private function getRepository()
    {
        return $this->container->get('app.secret.repository');
    }
}
