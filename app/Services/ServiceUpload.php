<?php

namespace App\Services;

use App\Helpers\HelperDate;
use App\Library\S3Storage;
use App\Models\FileData;
use App\Models\FileUpload;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class ServiceUpload
{
    public function __construct()
    {
    }

    public function log(Request $request): array
    {
        $name = $request->get('name') ?? null;
        $date = ($request->get('date') !== null) ? HelperDate::convertDate($request->get('date')) : null;
        $file = FileUpload::select('_id', 'name', 'date', 'path', 'size', 'type', 'extension', 'processed');
        return match (true) {
            $name !== null || $date !== null => [
                'data' => $file->whereLike('name', '%' . $name . '%')
                    ->orwhereDate('date', $date)->get(),
                'status' => Response::HTTP_OK
            ],
            default => [
                'data' => $file->get(),
                'status' => Response::HTTP_OK
            ]
        };
    }

    public function getData(Request $request): ?array
    {
        $TckrSymb = $request->query('TckrSymb') ?? null;
        $RptDt = ($request->query('RptDt') !== null)
            ? HelperDate::convertDate($request->query('RptDt'))
            : null;

        return match (true) {
            $TckrSymb !== null && $RptDt === null => [
                'data' => FileData::where('TckrSymb', $TckrSymb)->get(),
                'status' => Response::HTTP_OK
            ],
            $RptDt !== null && $TckrSymb === null => [
                'data' => FileData::whereDate('RptDt', $RptDt)->get(),
                'status' => Response::HTTP_OK
            ],
            $TckrSymb !== null && $RptDt !== null => [
                'data' => FileData::where('TckrSymb', $TckrSymb)->whereDate('RptDt', $RptDt)->get(),
                'status' => Response::HTTP_OK
            ],
            default => [
                'data' => FileData::paginate(50),
                'status' => Response::HTTP_OK
                ]
        };
    }
    /**
     * @throws Exception
     */
    public function save(Request $request): array
    {
        $originalName = '';
        if ($request->hasFile('fileUpload')) {
            $file = $request->file('fileUpload');
            $originalName = $file->getClientOriginalName();
        }
        $files = FileUpload::select('name')->get();
        foreach ($files as $file) {
            if ($file->name === $originalName) {
                throw new Exception('File already exists', Response::HTTP_BAD_REQUEST);
            }
        }
        $data = S3Storage::s3StoreFile($request);
        $result = FileUpload::create($data);
        $queue = [
            '_id' => $result->_id,
            'name' => $result->name,
            'path' => $result->path
        ];
        $queueJson = json_encode($queue, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        Redis::publish('upload', $queueJson);
        return ['data' => $result, 'status' => Response::HTTP_CREATED];
    }

    /**
     * @throws Exception
     */
    public function delete($id): ?array
    {
        $file = FileUpload::with('fileData')->find($id);
        if ($file === null) {
            throw new Exception('File not found', Response::HTTP_NOT_FOUND);
        }
        if (S3Storage::s3StoreFileDelete($file->path)) {
            $data = $file->fileData;
            $data->each(fn ($d) => $d->get()->each(fn ($d) => $d->delete()));
        }
        return ['data' => $file->delete(), 'status' => Response::HTTP_NO_CONTENT];
    }
}
