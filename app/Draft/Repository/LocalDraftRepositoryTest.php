<?php

namespace App\Draft\Repository;

use App\Draft\Draft;
use App\Testing\TestCase;
use App\Testing\TestDrafts;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class LocalDraftRepositoryTest extends TestCase
{
    protected $draftsToCleanUp = [];

    #[Test]
    #[DataProviderExternal(TestDrafts::class, 'provideSingleTestDraft')]
    public function itCanSaveADraft($data)
    {
        $d = Draft::fromJson($data);
        $repository = new LocalDraftRepository();

        $repository->save($d);

        $this->draftsToCleanUp[] = $d->id;

        $draftPath = 'tmp/test-drafts/draft_' . $d->id . '.json';

        $this->assertFileExists($draftPath);
        $content = json_decode(file_get_contents($draftPath));

        $this->assertSame($content->id, $d->id);
        $this->assertSame($content->draft->current, $d->currentPlayerId->value);
    }

    #[Test]
    #[DataProviderExternal(TestDrafts::class, 'provideSingleTestDraft')]
    public function itCanLoadADraft($data)
    {
        $draftPath = 'tmp/test-drafts/draft_' . $data['id'] . '.json';
        file_put_contents($draftPath, json_encode($data));
        $this->draftsToCleanUp[] = $data['id'];
        $repository = new LocalDraftRepository();

        $draft = $repository->load($data['id']);

        $this->assertSame($draft->id, $data['id']);
        $this->assertSame($draft->currentPlayerId->value, $data['draft']['current']);
    }

    #[After]
    protected function cleanupAfterTests()
    {
        $r = new LocalDraftRepository();
        foreach ($this->draftsToCleanUp as $id) {
            $r->delete($id);
        }
    }
}