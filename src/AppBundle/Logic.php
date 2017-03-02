<?php

namespace AppBundle;

use Symfony\Component\HttpFoundation\Request;

class Logic
{
    /**
     * @param Request $request
     */
    public function init($request)
    {
        $this->setActualPageNumber($request, 1);
    }

    /**
     * Next page - increase page number
     * @param Request $request
     * @param $pageCount
     * @return int Actual page number after performing action
     */
    public function nextPageNumber($request, $pageCount)
    {
        $actualPage = $this->getActualPageNumber($request);
        if ($actualPage < $pageCount) {
            $actualPage++;
        }
        return $this->setActualPageNumber($request, $actualPage);
    }

    /**
     * Previous page - decrease page number
     * @param Request $request
     * @return int Actual page number after performing action
     */
    public function prevPageNumber($request)
    {
        $actualPage = $this->getActualPageNumber($request);
        if ($actualPage > 1) {
            $actualPage--;
        }
        return $this->setActualPageNumber($request, $actualPage);
    }

    /**
     * Get actual page number
     * @param Request $request
     * @return int Page number
     */
    public function getActualPageNumber($request)
    {
        $actualPage = $request->getSession()->get('actualPage');
        if (gettype($actualPage) != 'integer') {
            $this->init($request);
        }
        return $actualPage;
    }

    /**
     * @param Request $request
     * @param int $page
     * @return mixed
     */
    public function setActualPageNumber($request, $page)
    {
        $request->getSession()->set('actualPage', $page);
        return $page;
    }
}
