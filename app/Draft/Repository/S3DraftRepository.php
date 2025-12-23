<?php

namespace App\Draft\Repository;

use App\Draft\Draft;
use App\Draft\Exceptions\DraftRepositoryException;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class S3DraftRepository implements DraftRepository
{
    private readonly S3Client $client;
    private readonly string $bucket;

    public function __construct()
    {
        $this->client =  new S3Client([
            'version' => 'latest',
            // @todo fix this?
            'region'  => 'us-east-1',
            'endpoint' => 'https://' . env('REGION') . '.digitaloceanspaces.com',
            'credentials' => [
                'key'    => env('ACCESS_KEY'),
                'secret' => env('ACCESS_SECRET'),
            ],
        ]);
        $this->bucket = env('BUCKET');
    }

    protected function draftKey(string $id): string
    {
        return 'draft_' . $id . '.json';
    }

    public function load(string $id): Draft
    {
        if (!$this->client->doesObjectExist($this->bucket, $this->draftKey($id))) {
            throw DraftRepositoryException::notFound($id);
        }

        $file = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key'    => $this->draftKey($id),
        ]);

        $rawDraft = (string) $file['Body'];

        return Draft::fromJson(json_decode($rawDraft, true));
    }

    public function save(Draft $draft)
    {
        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key'    => $this->draftKey($draft->id),
            'Body'   => $draft->toFileContent(),
            'ACL'    => 'private'
        ]);
    }

    public function delete(string $id)
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key'    => $this->draftKey($id),
        ]);
    }
}