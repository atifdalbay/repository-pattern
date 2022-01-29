<?php

namespace App\Http\FormRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


abstract class  AbstractRequest
{
	private $requestData;
	private $errorStatus = false;
	private $errorMessage;
	private $validator;

	public function __construct (Request $request)
	{

		$this->requestData = $request;
 		$this->makeValidate ();

	}

	abstract function rules (): array;

	public function request ()
	{

		return $this->requestData;

	}


    public function getFormData()
    {
        return $this->requestData->all();
    }

	public function makeValidate ()
	{

		$this->validator = Validator::make ($this->requestData->all (), $this->rules ());



		if ($this->validator->fails ()) {

			$this->errorMessage = $this->validator->getMessageBag ();

			$this->errorStatus = true;


		}

	}


	public function toArray ()
	{
        $returnResult =[];

        $validData= $this->validator->validated ();
	    if($this->validator->validated()){

	        foreach($this->rules() as $key=> $rules){

	                try{

	                    $returnResult[$key] = $validData[$key]!=""? $validData[$key]: "";
                    }catch(\Exception $e){

                        $returnResult[$key] = "";
                    }
	        }
        }
		return $returnResult;
	}

	public function error ()
	{

		return $this->errorStatus;

	}

	public function getErrorMessage ()
	{

		return $this->errorMessage;

	}

	public function getValidator ()
	{

		return $this->validator;
	}
}