<?php

namespace App\Http\Controllers\Api\Upload;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\LogFileRequest;
use App\Services\ServiceUpload;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    protected ServiceUpload $serviceUpload;
    public function __construct(ServiceUpload $serviceUpload)
    {
        $this->serviceUpload = $serviceUpload;
    }
    /**
     * Display a listing of the resource.
     */
    public function log(LogFileRequest $request)
    {
        try {
            $result = $this->serviceUpload->log($request);
        } catch (\Exception $e) {
            $result = [
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ];
        }
        return response()->json($result, $result['status'], [], JSON_UNESCAPED_SLASHES);
    }
    public function getData(Request $request)
    {
        try {
            $result = $this->serviceUpload->getData($request);
        } catch (\Exception $e) {
            $result = [
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ];
        }
        return response()->json($result, $result['status'], [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save(FileUploadRequest $request)
    {
        try {
            $result = $this->serviceUpload->save($request);
        } catch (\Exception $e) {
            $result = [
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ];
        }
        return response()->json($result, $result['status'], [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        try {
            $result = $this->serviceUpload->delete($id);
        } catch (\Exception $e) {
            $result = [
                'message' => $e->getMessage(),
                'status' => $e->getCode()
            ];
        }
        return response()->json($result, $result['status'], [], JSON_UNESCAPED_SLASHES);
    }
}
