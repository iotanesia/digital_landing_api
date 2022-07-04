<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiHelper as Helper;
use App\Query\Auth\User;

class UserControler extends Controller
{
    public function getAll(Request $request)
    {
        try {
            return Helper::resultResponse(
                User::getAllData($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    public function getById(Request $request,$id)
    {
        return Helper::responseData(
            // User::byId($id)
        );
    }

    public function save(Request $request)
    {

        try {
            return Helper::resultResponse(
                User::saveData($request)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }

    }

    public function update(Request $request,$id)
    {
        try {
            return Helper::responseData(
                User::updateData($request,$id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    public function delete(Request $request,$id)
    {
        try {
            return Helper::responseData(
                User::deleteData($id)
            );
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    public function detail(Request $request) {
        try {
            $user = Helper::getUserJwt($request);
            return Helper::resultResponse( User::byId($user->id));
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }
}
