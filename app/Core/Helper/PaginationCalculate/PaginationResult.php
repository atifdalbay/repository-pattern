<?php


namespace App\Core\Helper\PaginationCalculate;

class PaginationResult
{
    private $pageNo;
    private $offset;
    private $limit;
    private $totalPage;

    public function __construct(int $pageNo, int $offset, int $limit, int $totalPage)
    {
        $this->pageNo    = $pageNo;
        $this->offset    = $offset;
        $this->limit     = $limit;
        $this->totalPage = $totalPage;
    }

    /**
     * @return int
     */
    public function getPageNo(): int
    {
        return $this->pageNo;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getTotalPage(): int
    {
        return $this->totalPage;
    }
}
