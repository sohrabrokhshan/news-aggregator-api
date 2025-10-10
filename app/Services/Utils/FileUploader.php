<?php

namespace App\Services\Utils;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\FileNotFoundException;
use App\Enums\UploadDisk;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;

class FileUploader
{
    private FilesystemAdapter $storage;

    public function __construct(
        private readonly UploadDisk $disk,
        private readonly string $uploadPath
    ) {
        return $this->storage = Storage::disk($disk->value);
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = Str::random(50) . '.' . $file->getClientOriginalExtension();
        $this->storage->putFileAs($this->uploadPath, $file, $fileName);
        return $fileName;
    }

    public function download(string $fileName): StreamedResponse
    {
        $path = $this->getPath($fileName);

        if (!$this->storage->exists($path)) {
            throw new FileNotFoundException();
        }

        return $this->storage->download($path, Str::random(20));
    }

    public function delete(string $fileName): void
    {
        $this->storage->delete($this->getPath($fileName));
    }

    private function getPath(string $fileName): string
    {
        return $this->uploadPath . '/' . $fileName;
    }
}
