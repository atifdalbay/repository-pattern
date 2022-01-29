<?php

namespace App\Core\Helper\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class CheckExists implements Rule
{
	private $appModel;
	private $attribute;
	private $hasParent;

	public function __construct ($appModel, $hasParent = 1)
	{
		$this->appModel = App::make ($appModel);
		$this->hasParent = $hasParent;
	}

	public function passes ($attribute, $value)
	{

		$this->attribute = $attribute;

		$result = $this->appModel->find ($value);

		if ($this->hasParent == 0 and $attribute == 0) {
			return true;
		}
		return $result == null ? false : true;
	}

	public function message ()
	{
		return [$this->attribute . ' ' . Lang::get("Rules.NotFound")];
	}
}
