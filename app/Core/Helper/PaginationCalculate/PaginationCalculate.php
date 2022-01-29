<?php
declare(strict_types = 1);

namespace App\Core\Helper\PaginationCalculate;

class PaginationCalculate
{

    private function calculatePageNo(int $pageNo): int
    {
        if( $pageNo <= 0 ){
            $pageNo = 1;
        }
        return $pageNo;
    }

    private function calculateOffset(int $pageNo, int $pageItem): int
    {
        $offset = ($pageNo - 1) * $pageItem;
        if( $offset < 0 ){
            $offset = 0;
        }

        return $offset;
    }

    private function calculateTotalPage(int $totalItem, $pageItem): int
    {
        return (int) ceil($totalItem / $pageItem);
    }

    public function calculate(int $pageNo, int $pageItem, int $totalItem): PaginationResult
    {
        $pageNo = $this->calculatePageNo($pageNo);
        $result =
            [
                "pageNo"    => $pageNo,
                "offset"    => $this->calculateOffset($pageNo, $pageItem),
                "limit"     => $pageItem,
                "totalPage" => $this->calculateTotalPage($totalItem, $pageItem)
            ];

        return new PaginationResult(...array_values($result));
    }

}
