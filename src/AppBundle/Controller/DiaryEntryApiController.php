<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Session\Session;

class DiaryEntryApiController extends FOSRestController
{
    /**
     * @Rest\Get("/api/entry/{page}", name="get_entry")
     * @param $page
     * @return array|View|null|object
     */
    public function getEntryAction($page)
    {
        $diaryEntries = $this->getDoctrine()->getRepository('AppBundle:DiaryEntry')->getAllEntries($page, 1);
        if ($diaryEntries === null || $diaryEntries->getIterator()->count() == 0) {
            return new View("Entry not found", Response::HTTP_NOT_FOUND);
        }
        $diaryEntry = $diaryEntries->getIterator()[0];
        $result = [];
        $result['datetime'] = $diaryEntry->getDateTime();
        $result['title'] = $diaryEntry->getTitle();
        $result['note'] = $diaryEntry->getNote();
        return $result;
    }

    /**
     * @Rest\Get("/api/entry-next", name="get_next_entry")
     * @return array|View|null|object
     */
    public function getNextEntryAction()
    {
        $session = new Session();
        $session->start();
        $actualPage = $session->get('actualPage');
        $session->set('actualPage', $actualPage + 1);
        return [$session->get('actualPage')];
    }
}
