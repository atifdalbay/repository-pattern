<?php

namespace App\Repository;

use App\Core\Helper\UrlQueryParser\FilterParser;
use App\Core\Helper\UrlQueryParser\PageParser;
use App\Core\Helper\PaginationCalculate\PaginationCalculate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

abstract class AbstractRepository
{
	private $defaultModel;



	abstract protected function getDefaultModel(): string;

	public function __construct()
	{

		$this->defaultModel = App::make($this->getDefaultModel());
	}

	public function defaultModel()
	{

		return $this->defaultModel;
	}

	public function toArray()
	{

		return $this->defaultModel->get()->all();
	}



	public function find($id, array $selectField = [])
	{
		$primaryKey = $this->defaultModel->getKeyName();
		return $this->findByField($primaryKey, $id, $selectField);
	}


	public function getAllCollection()
	{

		$modelData = $this->toArray();
		$modelDataArray = [];

		foreach ($modelData as $mD) {

			$dataClass = $mD->getOriginal();
			$modelDataArray[] = $dataClass;
		}

		return $modelDataArray;
	}

	public function findByField(string $field, $value, array $selectField = [])
	{

		$result = $this->defaultModel->where($field, "=", $value);
		if ($selectField) {
			$result->select($selectField);
		}
		$result = $result->first();
		return $result ? ($result) : null;
	}

	public function findByFieldAll(string $field, $value, array $selectField = [])
	{
		$result = $this->defaultModel->where($field, "=", $value);
		if ($selectField) {
			$result->select($selectField);
		}

		$result = $result->get()
			->toArray();
		return $result ? ($result) : null;
	}
	public function findByFieldMultiResult(string $field, $value, array $selectField = [])
	{

		$result = $this->defaultModel->where($field, "=", $value);
		if ($selectField) {
			$result->select($selectField);
		}
		$result->get()
			->toArray();
		return $result ? ($result) : null;
	}


	public function findIn(array $ids): Collection
	{

		$primaryKey = $this->defaultModel->getKeyName();
		return $this->findInByField($primaryKey, $ids, $this->defaultModel);
	}

	public function findInByField(string $field, array $values, array $selectField = []): Collection
	{
		$item = $this->defaultModel->whereIn($field, $values);
		if ($selectField) {
			$item->select($selectField);
		}
		return $item->get()
			->toArray();
	}

	public function findByFieldAndCompany($id, $company_id, array $selectField = [])
	{
		$result = $this->defaultModel()->where("id", "=", $id)
			->where("company_id", "=", $company_id);
		if ($selectField) {
			$result->select($selectField);
		}
		$result = $result->first();
		return $result ? ($result) : null;
	}

	public function calculatePagination($page = 1)
	{

		$defaultPageLimit = 25;
		$paginationCalculate = App::make(PaginationCalculate::class);

		$paginationResult = $paginationCalculate->calculate(($page),
			$defaultPageLimit,
			$this->defaultModel::count()
		);

		$limit = $paginationResult->getLimit();
		$offset = $paginationResult->getOffset();
		$totalPage = $paginationResult->getTotalPage();

		return ["limit" => $limit, "offset" => $offset, "totalPage" => $totalPage];
	}

	public function getPaginationResult(array $manuelFilter = [], array $relationShips = [], array $selectField = [])
	{

		$filterParser = new FilterParser($this->getColumnsName(), $manuelFilter);
		$filterData = $filterParser->getResultValue();
		if (data_get($filterData, "statusCode")) {
			return false;
		}
		$pageParser = new PageParser();
		$calculateResults = $this->calculatePagination((int)$pageParser->getMatchResult());
		$result  = [];
		$items = $this->defaultModel->newQuery()
			->where(data_get($filterData, "where"))
			->limit($calculateResults["limit"])
			->offset($calculateResults["offset"]);
		if ($selectField) {
			$items->select($selectField);
		}
		if (data_get($filterData, "sorting")) {
			$orderData = data_get($filterData, "sorting");
			$items->orderBy($orderData[0], $orderData[1]);
		}
		
		$items->with($relationShips);
		
		foreach (data_get($filterData, "whereIn") as $whereIn) {
			$items->whereIn($whereIn[0], $whereIn[1]);
		}
		//->toSql (); //Sql görüntelemek için get()->toArray()'lerden önce kullanılabilir
		$result["items"] = $items->get()->toArray();
		$result["resultCount"] = count($result);
		$result["totalPage"] = $calculateResults["totalPage"];
		$result["pageNo"] = $pageParser->getMatchResult();
		return $result;
	}


	public function getColumnsName()
	{

		$columns = ["valid" => Schema::getColumnListing($this->defaultModel->getTable()), "invalid" => isset($this->unWhereField) ? $this->unWhereField : []];

		return $columns;
	}
}
