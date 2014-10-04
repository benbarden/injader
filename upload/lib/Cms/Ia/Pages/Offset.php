<?php


namespace Cms\Ia\Pages;


class Offset
{
    /**
     * @var integer
     */
    private $pageNo;

    /**
     * @var integer
     */
    private $perPage;

    public function setPageNo($pageNo)
    {
        $this->pageNo = (int) $pageNo;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = (int) $perPage;
    }

    public function calculate()
    {
        if (!$this->pageNo) return 0;
        if (!$this->perPage) return 0;

        if ($this->pageNo < 2) return 0;

        $offset = ($this->pageNo - 1) * $this->perPage;
        return $offset;
    }
} 