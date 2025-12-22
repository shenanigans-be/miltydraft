<?php

namespace App\Draft\Repository;

use App\Draft\Draft;
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

    public function load(string $id): Draft
    {
        $file = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key'    => 'draft_' . $id . '.json',
        ]);

        $rawDraft = (string) $file['Body'];

        return Draft::fromJson($rawDraft);
    }

    public function save(Draft $draft)
    {
        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key'    => 'draft_' . $draft->id . '.json',
            'Body'   => $draft->toFileContent(),
            'ACL'    => 'private'
        ]);
    }
}