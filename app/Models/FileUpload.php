<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use MongoDB\Laravel\Relations\HasMany;

class FileUpload extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected string $collection = 'files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        '_id',
        'name',
        'date',
        'path',
        'size',
        'type',
        'processed',
    ];
    protected $casts = [
        'date' => 'date',
    ];

    public function fileData(): HasMany
    {
        return $this->hasMany(FileData::class);
    }
}
