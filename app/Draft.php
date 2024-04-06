<?php
// Could be cool to add a faction ban phase before the draft starts

namespace App;

class Draft implements \JsonSerializable
{
    private static self $instance;
    private bool $done;

    private function __construct(
        private string $id,
        private string $admin_pass,
        private array $draft,
        private array $slices,
        private array $factions,
        private GeneratorConfig $config,
        private string $name
    ) {
        $this->draft = ($draft === [] ? [
            'players' => $this->generatePlayerData(),
            'log' => [],
        ] : $draft);

        $this->done = $this->isDone();
        $this->draft["current"] = $this->currentPlayer();
    }

    public static function createFromConfig(GeneratorConfig $config)
    {
        $id = uniqid();
        $admin_password = uniqid();
        $slices = Generator::slices($config);
        $factions = Generator::factions($config);

        $name = $config->name;

        return new self($id, $admin_password, [], $slices, $factions, $config, $name);
    }

    public static function getCurrentInstance(): self
    {
        return self::$instance;
    }

    public static function load($id): self
    {
        if (!$id) {
            throw new \Exception('Tried to load draft with no id');
        }

        $draft = json_decode(file_get_contents(get_draft_url($id)), true);
        return new self($id, $draft["admin_pass"], $draft["draft"], $draft["slices"], $draft["factions"], GeneratorConfig::fromArray($draft["config"]), $draft["name"]);
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

    public function name(): string
    {
        return $this->name;
    }

    public function slices(): array
    {
        return $this->slices;
    }

    public function factions(): array
    {
        return $this->factions;
    }

    public function config(): GeneratorConfig
    {
        return $this->config;
    }

    public function currentPlayer(): string
    {
        $doneSteps = count($this->draft['log']);
        $snakeDraft = array_merge(array_keys($this->draft['players']), array_keys(array_reverse($this->draft['players'])));
        return $snakeDraft[$doneSteps % count($snakeDraft)];
    }

    public function log(): array
    {
        return $this->draft['log'];
    }

    public function players(): array
    {
        return $this->draft['players'];
    }

    public function isDone(): bool
    {
        return count($this->log()) >= (count($this->players()) * 3);
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

        $this->draft['current'] = $this->currentPlayer();

        $this->done = $this->isDone();

        $this->save();
    }

    public function claim($player): self
    {
        if ($this->draft['players'][$player]["claimed"] == true) {
            return_error('Already claimed');
        }
        $this->draft['players'][$player]["claimed"] = true;

        $this->save();

        return $this;
    }

    public function unclaim($player): self
    {
        if ($this->draft['players'][$player]["claimed"] == false) {
            return_error('Already unclaimed');
        }
        $this->draft['players'][$player]["claimed"] = false;

        $this->save();

        return $this;
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
        if ($regen_factions) {
            $this->factions = Generator::factions($this->config);
        }

        if ($regen_slices) {
            $this->slices = Generator::slices($this->config);
        }

        if ($regen_order) {
            $this->draft['players'] = $this->generatePlayerData();
        }

        $this->save();
    }

    private function generatePlayerData()
    {
        $player_data = [];

        $alliance_mode =  $this->config->alliance != null;

        if ($alliance_mode) {
            $playerTeams = $this->generateTeams();
        } else {
            shuffle($this->config->players);
        }


        $player_names = $this->config->players;

        foreach ($player_names as $p) {
            // use admin password and player name to hash an id for the player
            $id = 'p_' . md5($p . $this->admin_pass);

            $player_data[$id] = [
                'id' => $id,
                'name' => $p,
                'claimed' => false,
                'position' => null,
                'slice' => null,
                'faction' => null,
                'team' => $alliance_mode ? $playerTeams[$p] : null
            ];
        }

        return $player_data;
    }

    private function generateTeams(): array
    {
        $teamNames = array_slice(['A', 'B', 'C', 'D'], 0, count($this->config->players) / 2);

        if ($this->config->alliance["alliance_teams"] == 'random') {
            shuffle($this->config->players);
        }

        $teams = [];
        $currentTeam = [];
        $i = 0;
        // put players in teams
        while(count($teamNames) > 0) {
            $currentTeam[] = $this->config->players[$i];
            $i++;

            // if we filled up a team add it to the teams array with an unused name
            if(count($currentTeam) == 2) {
                $name = array_shift($teamNames);
                // randomise order of team
                shuffle($currentTeam);
                $teams[$name] = $currentTeam;
                $currentTeam = [];
            }
        }

        // determine team order
        // + put em in dictionary to map player names to team
        $playerTeams = [];
        $teamNames = array_keys($teams);
        shuffle($teamNames);
        $newPlayerOrder = [];

        foreach($teamNames as $n) {
            foreach($teams[$n] as $player) {
                $newPlayerOrder[] = $player;
                $playerTeams[$player] = $n;
            }
        }

        // violates immutability a bit, whoopsie...
        $this->config->players = $newPlayerOrder;

        return $playerTeams;
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
