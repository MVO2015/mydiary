<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class TagController extends FOSRestController
{
    /**
     * @Rest\Get("/tag/{id}")
     * @param $id
     * @return View|null|object
     */
    public function idAction($id)
    {
        $singleResult = $this->getDoctrine()->getRepository('AppBundle:Tag')->find($id);
        if ($singleResult === null) {
            return new View("tag not found", Response::HTTP_NOT_FOUND);
        }
        return $singleResult;
    }

    /**
     * @Rest\Get("/tag/")
     * @param Request $request
     * @return array|View
     * @internal param $data
     */
    public function searchAction(Request $request)
    {
        $search = $request->query->get('search');
        $repo = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $query = $repo->createQueryBuilder('tag')
            ->where('tag.text LIKE :param')
            ->setParameter('param', $search.'%')
            ->getQuery();
        $dataItems = $query->getResult();
        $result = ['total_count' => count($dataItems), 'items' => $dataItems];
        if ($dataItems === null) {
            return new View("tag not found", Response::HTTP_NOT_FOUND);
        }
        return $result;
    }
}
