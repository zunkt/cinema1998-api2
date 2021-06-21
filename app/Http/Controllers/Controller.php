<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const PHONE_REGISTERED = 201;
    const VERIFY_FAILURE = 202;
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($code = 200, $data = [], $message = '', $errors = [], $extendCode = null)
    {
        return response()->json([
            'code' => $extendCode ? $extendCode : $code,
            'data' => (object)$data,
            'message' => $errors ? array_values($errors->toArray())[0][0] : $message,
            'errors' => (object)$errors
        ], $code);
    }

    public function getSortDesc(){
        if(request()->sortDesc == ''){
            return 'desc';
        }

        $sortDesc = filter_var(request()->sortDesc, FILTER_VALIDATE_BOOLEAN);
        return $sortDesc ? 'desc' : 'asc';
    }

    public function getItemsPerPage($limit = 10){
        $itemPerPage = 0;

        if(request()->has('itemsPerPage')){
            $itemPerPage = request()->itemsPerPage ? intval(request()->itemsPerPage) : $limit;
        }else{
            $itemPerPage = request()->items_per_page ? intval(request()->items_per_page) : $limit;
        }

        return ($itemPerPage == -1) ? 100000 : $itemPerPage;
    }

    public function getSortBy($defaulSort = 'id'){
        if(request()->sortBy == '' || request()->sortBy == 'actions'){
            return $defaulSort;
        }

        return str_replace('_display', '', request()->sortBy);
    }

    public function validateRulePassword($password)
    {
        return true;
    }
}
