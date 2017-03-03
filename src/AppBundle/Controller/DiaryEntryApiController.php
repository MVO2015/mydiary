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
        /** @var DiaryEntry $diaryEntry */
        $diaryEntry = $this->getDoctrine()->getRepository('AppBundle:DiaryEntry')->find($id);
        return $this->getDataForApi($diaryEntry);
    }

    /**
     * @Rest\Get("/api/entry/offset/{offset}", name="get_entry_by_offset")
     * @param Request $request
     * @param $offset
     * @return array
     */
    public function getEntryByOffsetAction(Request $request, $offset)
    {
        /** @var DiaryEntry $diaryEntry */
        $diaryEntry = $this->getDoctrine()->getRepository('AppBundle:DiaryEntry')->getEntryByOffset($offset);
        return $this->getDataForApi($diaryEntry);
    }

    /**
     * Get data in structure suitable for API
     * @param DiaryEntry $diaryEntry
     * @return array
     */
    private function getDataForApi($diaryEntry)
    {
        $result = [];
        $result['datetime'] = $diaryEntry->getDateTime();
        $result['title'] = $diaryEntry->getTitle();
        $result['note'] = $diaryEntry->getNote();
        return $result;
    }
}
