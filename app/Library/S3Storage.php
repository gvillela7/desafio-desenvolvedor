<?php

namespace App\Library;

use App\Helpers\HelperDate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class S3Storage
{
    /**
     * @throws Exception
     */
    public static function s3StoreFile(Request $request): array
    {
        $file = $request->file('fileUpload');
        try {
            $s3 = $file->storeAs('/', $file->getClientOriginalName());
        } catch (Exception $e) {
            Log::debug('Error: ' . $e->getMessage());
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $fileDateName = explode('_', $file->getClientOriginalName());
        $fileDate = date('Y-m-d', strtotime($fileDateName[1]));
        return [
            'name' => $file->getClientOriginalName(),
            'date' => HelperDate::convertDate($fileDate),
            'path' => ('https://desafioot.s3.amazonaws.com/' . $s3),
            'size' => $file->getSize(),
            'type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'processed' => false
        ];
    }

    /**
     * @throws Exception
     */
    public static function s3StoreFileDelete($path): bool
    {
        $path = substr($path, 35);
        if ($path) {
            try {
                Storage::disk('s3')->delete($path);
            } catch (Exception $e) {
                Log::debug('Error: ' . $e->getMessage());
                throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return true;
    }
}
