<?php

namespace App\Testing;

enum TestDrafts: string
{
    case FINISHED_ALL_CHECKBOXES = "draft.november2025.finished.json";
    case WITH_CUSTOM_SLICES = "draft.november2025.custom.json";
    case ALLIANCE_MODE = "draft.november2025.alliance.json";

    private static function loadDraftByFilename(string $filename): array {
        return json_decode(file_get_contents('data/test-drafts/' . $filename . '.json'));
    }

    public static function testDraftsProvider(): iterable
    {
        foreach(TestDrafts::cases() as $case) {
            yield $case->name => [
                'data' => self::loadDraftByFilename($case->value)
            ];
        }
    }
}