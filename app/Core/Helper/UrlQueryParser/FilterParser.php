<?php


namespace App\Core\Helper\UrlQueryParser;

class FilterParser extends QueryParser
{
	protected $resultValue;

	public const equals = "eq";
	public const greaterThan = "gt";
	public const lessThan = "lt";
	public const greaterThanEqual = "gte";
	public const lessThanEqual = "lte";
	public const like = "like";
	public const in = "in";

	public function queryComparationConventer($value)
	{

		$comperetionArray = ["eq" => "=", "gt" => ">", "gte" => ">=", "lt" => "<", "lte" => "<=", "like" => "like", "in" => "in"];


		return $comperetionArray[$value];
	}

	public function __construct(array $filterColumns, array $manuelFilters = [])
	{

		$whereQueryArrays = [];
		$orderQueryArray = [];
		$whereInQueryArrays = [];

		if (strpos($this->urlParser(), "filter=(") !== false) {

			$urlParseByFilterFirst = explode("filter=(", $this->urlParser())[1];
			$urlParseByFilterTwo = urldecode(explode(")", $urlParseByFilterFirst)[0]);

			preg_match_all('/([a-zA-Z\_\-0-9]+)\:(eq|gt|gte|lt|lte|like|in)\:\[(.*?)\]/', $urlParseByFilterTwo, $output_array);

			if (count(array_intersect($output_array[1], $filterColumns['invalid'])) > 0) {
				//throw new \Exception("Not Valid Column Error", 403);
				$this->resultValue = [
					"statusCode" => 400
				];
				return 0;
			}

			if (count(array_diff($output_array[1], $filterColumns['valid'])) > 0) {
				//throw new \Exception("Filter Error", 403);
				$this->resultValue = [
					"statusCode" => 400
				];
				return 0;
			}

			if (count($output_array[2]) != count($output_array[1])) {
				//throw new \Exception("Filter Query Type Error", 403);
				$this->resultValue = [
					"statusCode" => 400
				];
				return 0;
			}

			for ($queryParamsCount = 0; $queryParamsCount < count($output_array[2]); $queryParamsCount++) {

				if ($output_array[2][$queryParamsCount] == FilterParser::in) {
					$filterWhereInClouse = [
						strval($output_array[1][$queryParamsCount]),
						explode(",",strval($output_array[3][$queryParamsCount]))
					];
					$whereInQueryArrays[] = $filterWhereInClouse;
				} else {
					$filterWhereClouse = [
						strval($output_array[1][$queryParamsCount]),
						$this->queryComparationConventer($output_array[2][$queryParamsCount]),
						strval($output_array[3][$queryParamsCount])
					];

					$whereQueryArrays[] = $filterWhereClouse;
				}
			}
		}
		//Sorting Area
		if (strpos($this->urlParser(), "sorting=(") !== false) {
			$sortingUrlFilterFirst = explode("sorting=(", $this->urlParser())[1];
			$sortingUrlFilterTwo = urldecode(explode(")", $sortingUrlFilterFirst)[0]);
			preg_match('/([a-zA-Z\_\-0-9]+)\:(desc|asc)/', $sortingUrlFilterTwo, $sortingFilter);
			$orderQueryArray = [
				$sortingFilter[1],
				$sortingFilter[2]
			];
		}

		/**
		 * Manuel Search Array
		 */
		foreach ($manuelFilters as $manuelFilter) {
			$filterWhereClouse = [
				strval($manuelFilter[0]),
				$this->queryComparationConventer($manuelFilter[1]),
				strval($manuelFilter[2])
			];
			$whereQueryArrays[] = $filterWhereClouse;
		}

		$this->resultValue = [
			"where" => $whereQueryArrays,
			"whereIn" => $whereInQueryArrays,
			"sorting" => $orderQueryArray
		];
	}

	public function getResultValue(): array
	{

		return $this->resultValue != null ? $this->resultValue : [];
	}
}
