<?php


namespace Cms\Ia\Pages;


class LastPage
{
    /**
     * @var integer
     */
    private $itemCount;

    /**
     * @var integer
     */
    private $perPage;

    public function setItemCount($itemCount)
    {
        $this->itemCount = (int) $itemCount;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = (int) $perPage;
    }

    public function calculate()
    {
        if (!$this->itemCount) return 0;
        if (!$this->perPage) return 0;

        if ($this->itemCount <= $this->perPage) return 1;

        $lastPage = ceil($this->itemCount / $this->perPage);
        return $lastPage;
    }
} 