<?php

declare(strict_types=1);


namespace App\Core\Helper\UrlQueryParser;


class QueryParser
{
	public function urlParser()
	{

		$currentUrl = (app('url')->full()) . '?';
		$fullUrl        = (app('url')->full());
		try {
			$this->urlParams = explode("?", $fullUrl);
			if (count($this->urlParams) > 1) {
				$this->urlParams = $this->urlParams[1];
			} else {
				$this->urlParams = "";
			}
		} catch (\Exception $exception) {

			$this->urlParams = "";
		}

		return urldecode($this->urlParams);
	}
}
