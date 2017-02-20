<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class TagController
 * @package AppBundle\Controller
 */
class TagController extends Controller
{
    /**
     * @Route(
     *     "/tag/index/{orderBy}/{sort}",
     *     name="tag_index",
     *     defaults={"orderBy": "id", "sort": "asc"},
     *     requirements={"orderBy": "id|text", "sort": "asc|desc"},
     *     )
     * @param Request $request
     * @param string $orderBy Database column for order by clause
     * @param string $sort sorting parameter of database order by clause
     * @return Response
     */
    public function indexAction(Request $request, $orderBy="id", $sort="asc")
    {
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository("AppBundle:Tag")->findAllOrderBy($orderBy, $sort);
        return $this->render("tag/tagindex.html.twig", ['tags' => $tags]);
    }
}