<?php

namespace App\Services\Client;

use App\Enums\UploadDisk;
use App\Models\Client;
use App\Services\Interfaces\TrashModelServiceInterface;
use App\Services\Traits\TrashModelServiceTrait;
use App\Services\Utils\FileUploader;
use Illuminate\Support\Facades\Hash;

class UserService implements TrashModelServiceInterface
{
    use TrashModelServiceTrait;

    private FileUploader $fileUploader;

    public function __construct(
        private readonly Client $repository,
    ) {
        $this->fileUploader = new FileUploader(UploadDisk::PUBLIC, 'clients');
    }

    public function create(array $data): Client
    {
        $client = new Client();
        $client->password = empty($data['password']) ? null : Hash::make($data['password']);
        return $this->store($client, $data);
    }

    public function update(Client $client, array $data): Client
    {
        return $this->store($client, $data);
    }

    public function setPreferences(Client $client, array $preferences): void
    {
        $client->preferences = $preferences;
        $client->save();
    }

    private function store(Client $client, array $data): Client
    {
        $oldImage = $client->image;
        $newImage = $oldImage;

        if (key_exists('image', $data) && is_null($data['image'])) {
            $newImage = null;
        } else if (!empty($data['image'])) {
            $newImage = $this->fileUploader->upload($data['image']);
        }

        $client->first_name = $data['first_name'];
        $client->last_name = $data['last_name'];
        $client->email = $data['email'];
        $client->image = $newImage;
        $client->save();

        if ($oldImage && $oldImage !== $client->image) {
            $this->fileUploader->delete($oldImage);
        }

        return $client;
    }

    public function delete(Client $client): void
    {
        $client->delete();
    }

    public function findByEmail(string $email): ?Client
    {
        return $this->repository->where('email', $email)->first();
    }

    public function show(int $id): Client
    {
        return $this->getOne($id);
    }
}
