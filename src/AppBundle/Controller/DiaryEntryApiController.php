<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DiaryEntry;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class DiaryEntryApiController extends FOSRestController
{
    /**
     * @Rest\Get("/api/entry/id/{id}", name="get_entry_by_id")
     * @param Request $request
     * @param $id
     * @return array
     */
    public function getEntryByIdAction(Request $request, $id)
    {
        $diaryEntry = $this->getDoctrine()->getRepository('AppBundle:DiaryEntry')->find($id);
        $rowNumArr = $this->getDoctrine()->getRepository('AppBundle:DiaryEntry')->getRowNum($id);
        return $rowNumArr;
        /** @var DiaryEntry $nextEntry */
        $nextEntry = $this->getDoctrine()->getRepository('AppBundle:DiaryEntry')
            ->getAllEntries($rowNumArr[0]['id'], 1)->getIterator()->current();
        $result = [];
        $result['datetime'] = $diaryEntry->getDateTime();
        $result['title'] = $diaryEntry->getTitle();
        $result['note'] = $diaryEntry->getNote();
        $result['nextId'] = $nextEntry->getId();
        return $result;
    }

    /**
     * @Rest\Get("/api/entry/{pageNumber}", name="get_entry")
     * @param Request $request
     * @param $pageNumber
     * @return View|null|object
     */
    public function getEntryAction(Request $request, $pageNumber)
    {
        $this->get('app.logic')->setActualPageNumber($request, $pageNumber);
        return $this->getPageContent($pageNumber);
    }

    /**
     * @Rest\Get("/api/entry-next", name="get_next_entry")
     * @param Request $request Http request
     * @return array
     */
    public function getNextPageAction(Request $request)
    {
        $pageNumber = $this->get('app.logic')->nextPageNumber($request, 10);
        return $this->getPageContent($pageNumber);
    }

    /**
     * @Rest\Get("/api/entry-prev", name="get_prev_entry")
     * @param Request $request Http request
     * @return array
     */
    public function getPrevPageAction(Request $request)
    {
        $pageNumber = $this->get('app.logic')->prevPageNumber($request);
        return $this->getPageContent($pageNumber);
    }

    /**
     * Get page content for API
     * @param int $pageNumber
     * @return array|View
     */
    private function getPageContent($pageNumber)
    {
        $diaryEntries = $this->getDoctrine()->getRepository('AppBundle:DiaryEntry')->getAllEntries($pageNumber, 1);
        if ($diaryEntries === null || $diaryEntries->getIterator()->count() == 0) {
            return new View("Entry not found", Response::HTTP_NOT_FOUND);
        }
        /** @var DiaryEntry $diaryEntry */
        $diaryEntry = $diaryEntries->getIterator()->current();
        $result = [];
        $result['datetime'] = $diaryEntry->getDateTime();
        $result['title'] = $diaryEntry->getTitle();
        $result['note'] = $diaryEntry->getNote();
        return $result;
    }

    private function getNextIdAction($actualId)
    {
        /** @var DiaryEntry $diaryEntry */
        $nexId = $this->getDoctrine()->getRepository('AppBundle:DiaryEntry')->getNextEntryByDateTime($actualId);
        return $nexId;
    }
}
