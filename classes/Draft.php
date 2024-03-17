<?php

class Draft implements JsonSerializable
{
    private static self $instance;
    private string $id;
    private string $admin_pass;
    private string $name;
    private array $draft;
    private array $slices;
    private array $factions;
    private array $config;
    private bool $done;

    private function __construct($id, $admin_pass, $draft, $slices, $factions, $config, $name)
    {
        $this->id = $id;
        $this->admin_pass = $admin_pass;
        $this->draft = $draft;
        $this->slices = $slices;
        $this->factions = $factions;
        $this->config = $config;
        $this->name = $name;
        $this->done = $this->isDone();
        $this->draft["current"] = $this->getCurrentPlayer();
    }

    public static function createFromConfig(GeneratorConfig $config)
    {
        $id = uniqid();
        $admin_password = uniqid();
        $slices = select_slices($config);
        $factions = select_factions($config);
        $player_data = generatePlayerData($config->players, $admin_password);
        $name = $config->name;

        $draft = [
            'players' => $player_data,
            'log' => [],
        ];

        return new self($id, $admin_password, $draft, $slices, $factions, $config->toJson(), $name);
    }

    public static function getCurrentInstance(): self
    {
        return self::$instance;
    }

    public static function load($id): self
    {
        if (!$id) {
            return null;
        }
        $draft = json_decode(file_get_contents(get_draft_url($id)), true);
        return new self($id, $draft["admin_pass"], $draft["draft"], $draft["slices"], $draft["factions"], $draft["config"], $draft["name"]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAdminPass(): string
    {
        return $this->admin_pass;
    }

    public function isAdminPass(?string $pass): bool
    {
        return ($pass ?: "") === $this->admin_pass;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlices(): array
    {
        return $this->slices;
    }

    public function getFactions(): array
    {
        return $this->factions;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getCurrentPlayer(): string
    {
        $doneSteps = count($this->draft['log']);
        $snakeDraft = array_merge(array_keys($this->draft['players']), array_keys(array_reverse($this->draft['players'])));
        return $snakeDraft[$doneSteps % count($snakeDraft)];
    }

    public function getLog(): array
    {
        return $this->draft['log'];
    }

    public function getPlayers(): array
    {
        return $this->draft['players'];
    }

    public function isDone(): bool
    {
        return count($this->getLog()) >= (count($this->getPlayers()) * 3);
    }

    public function undoLastAction()
    {
        $last_log = array_pop($this->draft['log']);

        $this->draft["players"][$last_log['player']][$last_log['category']] = null;
        $this->draft['current'] = $last_log['player'];

        $this->save();
    }

    public function pick($player, $category, $value)
    {
        $this->draft['log'][] = [
            'player' => $player,
            'category' => $category,
            'value' => $value
        ];

        $this->draft['players'][$player][$category] = $value;

        $this->draft['current'] = $this->getCurrentPlayer();

        $this->done = $this->isDone();

        $this->save();
    }

    public function save()
    {
        if ($_ENV['STORAGE'] == 'local') {
            file_put_contents($_ENV['STORAGE_PATH'] . '/' . 'draft_' . $this->getId() . '.json', (string) $this);
        } else {
            $s3 = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region'  => 'us-east-1',
                'endpoint' => 'https://' . $_ENV['REGION'] . '.digitaloceanspaces.com',
                'credentials' => [
                    'key'    => $_ENV['ACCESS_KEY'],
                    'secret' => $_ENV['ACCESS_SECRET'],
                ],
            ]);


            $result = $s3->putObject([
                'Bucket' => $_ENV['BUCKET'],
                'Key'    => 'draft_' . $this->getId() . '.json',
                'Body'   => (string) $this,
                'ACL'    => 'public-read'
            ]);

            return $result;
        }
    }

    public function regenerate(bool $regen_slices, bool $regen_factions, bool $regen_order): void
    {
        $config = GeneratorConfig::fromDraft($this);

        if ($regen_factions) {
            $this->factions = select_factions($config);
        }

        if ($regen_slices) {
            $this->slices = select_slices($config);
        }

        if ($regen_order) {
            shuffle($this->config['players']);
            $player_data = generatePlayerData($this->config['players'], $this->admin_pass);
            $this->draft['players'] = $player_data;
        }

        $this->save();
    }

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
