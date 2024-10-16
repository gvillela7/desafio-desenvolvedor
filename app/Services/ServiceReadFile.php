<?php

namespace App\Services;

use App\Helpers\HelperDate;
use App\Helpers\HelperFile;
use App\Models\FileUpload;
use Generator;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;

class ServiceReadFile
{
    public function __construct()
    {
        //
    }

    /**
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws GuzzleException
     * @throws Exception
     */
    public function read($upload): Generator
    {

        $response = HelperFile::getFile($upload);
        if ($response->getStatusCode() === 200) {
            $data = Reader::createFromPath(public_path('storage/test.csv'), 'r');
            $data->setDelimiter(';');
            $data->setHeaderOffset(1);
        }
        $dataCollection =  LazyCollection::make(static fn () => yield from $data->getRecords())->toArray();
        $fileDataCollection = [];
        foreach ($dataCollection as $offset => $collection) {
            if ($offset === 0) {
                continue;
            }
            $fileDataCollection[] = [
                'RptDt' => HelperDate::convertDate($collection['RptDt']),
                'TckrSymb' => $collection['TckrSymb'],
                'MktNm' => $collection['MktNm'],
                'SctyCtgyNm' => iconv('UTF-8', 'UTF-8//IGNORE', $collection['SctyCtgyNm']),
                'ISIN' => $collection['ISIN'],
                'CrpnNm' => iconv('UTF-8', 'UTF-8//IGNORE', $collection['CrpnNm'])
            ];
        }
        yield $fileDataCollection;
    }

    /**
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws GuzzleException
     * @throws Exception
     */
    public function saveDataFile($upload): void
    {
        $gen = $this->read($upload);
        $uploadArray = json_decode($upload, true);
        $fileUpload = FileUpload::find($uploadArray['_id']);
        foreach ($gen->current() as $data) {
            $fileUpload->fileData()->create($data);
        }
        unlink(public_path('storage/test.csv'));
        $fileUpload->update(['processed' => true]);
        Log::info('File processed successfully');
    }
}
