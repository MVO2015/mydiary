<?php

namespace AppBundle;

class Logic
{
    /**
     * Actual diary page
     * @var int
     */
    private $actualPage;

    public function init()
    {
        $this->setActualPage(1);
    }

    /**
     * Next page - increase page number
     * @param $pageCount
     * @return int Actual page number after performing action
     */
    public function nextPage($pageCount)
    {
        if ($this->actualPage < $pageCount) {
            $this->actualPage = $this->actualPage + 1;
        }
        return $this->actualPage;
    }

    /**
     * Previous page - decrease page number
     * @return int Actual page number after performing action
     */
    public function prevPage()
    {
        if ($this->actualPage > 1) {
            $this->actualPage--;
        }
        return $this->actualPage;
    }

    /**
     * Get actual page number
     * @return int Page number
     */
    public function getActualPage()
    {
        return $this->actualPage;
    }

    public function setActualPage($page)
    {
        $this->actualPage = $page;
    }
}