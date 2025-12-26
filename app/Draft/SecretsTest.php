<?php

namespace App\Draft;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SecretsTest extends TestCase
{
    #[Test]
    public function itGeneratesRandomSecretsEvenIfSeedIsSet()
    {
        $previouslyGenerated = "kOFY/yBXdhP5cC97tlxPhQ==";
        mt_srand(123);
        $secret = Secrets::generateSecret();

        $this->assertNotSame($previouslyGenerated, $secret);
    }

    #[Test]
    public function itCanBeInitiatedFromJson()
    {
        $secretData = [
            'admin_pass' => 'secret124',
            'player_1' => 'secret456',
            'player_3' => 'secret789',
        ];

        $secret = Secrets::fromJson($secretData);

        $this->assertTrue($secret->checkAdminSecret('secret124'));
        $this->assertTrue($secret->checkPlayerSecret(PlayerId::fromString('player_1'), 'secret456'));
        $this->assertTrue($secret->checkPlayerSecret(PlayerId::fromString('player_3'), 'secret789'));
    }

    #[Test]
    public function itCanBeConvertedToArray()
    {
        $secrets = new Secrets(
            'secret124',
            [
                'player_1' => 'secret456',
                'player_3' => 'secret789',
            ]
        );
        $array = $secrets->toArray();

        $this->assertSame('secret124', $array['admin_pass']);
        $this->assertSame('secret456', $array['player_1']);
        $this->assertSame('secret789', $array['player_3']);

    }
}