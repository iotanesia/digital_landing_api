<?php

namespace App\Http\Controllers\Api;

use App\ApiHelper as Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($filename)
    {
        try {

            if(in_array($filename,['female.png','male.png'])) {
                $pathToFile = public_path().'/'.$filename;
            }else {
                $pathToFile = storage_path().'/app/'.$filename;
            }
            $file_exist = File::exists($pathToFile);
            if(!$file_exist) throw new \Exception("File tidak ditemukan", 400);

            if(!$file_exist){
                Log::debug('file not found '.$pathToFile);
                $pathToFile = storage_path().'/app/1111.pdf';
            }
            return Helper::responseDownload($pathToFile,$filename);
        } catch (\Throwable $th) {
            return Helper::setErrorResponse($th);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
