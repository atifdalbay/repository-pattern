<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;

abstract class AbstractController extends Controller
{
	public function redirectBackForFormCheck($backUrl, $withData)
	{

		return Redirect::to($backUrl)->withErrors($withData);
	}

	public function created($id)
	{

		return response()->json(["created_id" => $id, "statusCode" => '201'], 201);
	}

	public function notFound()
	{

		return response()->json(["status" => "notFound", "statusCode" => '404'], 404);
	}

	public function badRequest($index, array $data = null, $parameters = [], $request = null)
	{
		$respData['error'] = array_map(function ($field) {
			return $field[0];
		}, array_values($data));

		$response = [
			"statusCode" => Lang::get($index . '.statusCode'),
			"statusHeader" => Lang::get($index . '.statusHeader'),
			"statusMessage" => Lang::get($index . '.statusMessage', $parameters)
		];
		return response()->json(
			[
				"responseHeader" => $response,
				"responseData" => $respData,
				"requestData" => $request
			],
			Lang::get($index . '.statusCode')
		);
	}

	public function noContent()
	{

		return response()->json([], 200);
	}

	public function returnTrue()
	{
		return response()->json(["data" => true], 200);
	}

	public function returnFalse()
	{
		return response()->json(["data" => false], 200);
	}

	public function success(array $data)
	{
		return response()->json(["data" => $data, "statusCode" => '200'], 200);
	}

    public function responseDetail($index, $data = null, $parameters = [], $request = null)
    {
        $response = [
            "statusCode" => Lang::get($index . '.statusCode'),
            "statusHeader" => Lang::get($index . '.statusHeader'),
            "statusMessage" => Lang::get($index . '.statusMessage', $parameters),
            "statusVisibleType" => Lang::get($index . '.statusVisibleType')
        ];
        return response()->json(
            [
                "responseHeader" => $response,
                "responseData" => $data,
                "requestData" => $request
            ],
            Lang::get($index . '.statusCode')
        );
    }
}
