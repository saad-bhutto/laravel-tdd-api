<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    /**
     * generic helper method to throw the error reponse.
     *
     * @param string $message
     * @param \Throwable $exception
     * @return mixed
     */
    public function errorResponse(string $message = null, \Throwable $exception = null, $notifyException = true, $code = 400)
    {
        return response()->json($this->wrapArray([], [
            [
                'message' => $message,
                'exception' => is_null($exception) ? '' : $this->getExceptionMessage($exception),
            ],
        ]), $code);
    }

    /**
     * Get Readable Exception Message.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function getExceptionMessage(\Throwable $exception = null)
    {
        return $exception->getMessage() . ' at ' . $exception->getFile() . ':' . $exception->getLine();
    }
    /**
     * successResponse function to return 200 consitiant keys and values.
     *
     * @param mixed $data
     * @return mixed
     */
    public function successResponse($data)
    {
        return response()->json(
            $this->wrapArray($data)
        );
    }

    public function wrapArray($data, array $errors = [])
    {
        return [
            'status' => is_array($data) ? count($data) > 0 && count($errors) === 0 : !is_null($data),
            'data' => $data,
            'errors' => $errors,
        ];
    }
}
