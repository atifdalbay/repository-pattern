<?php


namespace App\Core\Helper\UrlQueryParser;


class PageParser extends QueryParser
{

	protected $matchResult;



	public function __construct ()
	{

		preg_match ('/\b(page=((\d+))\b)/', $this->urlParser (), $output_array);

		if ((count ($output_array)) > 0) {


			if ($output_array[3]) {

				$this->matchResult = $output_array[3];

			} else {

				$this->matchResult = 1;

			}
		}


		return $this->matchResult;
	}



	// Getter Method
	public function getMatchResult ()
	{

		return $this->matchResult != null ? $this->matchResult : 1;
	}


}
