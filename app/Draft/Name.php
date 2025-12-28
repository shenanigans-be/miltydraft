<?php

declare(strict_types=1);

namespace App\Draft;

class Name implements \Stringable
{
    private string $name;

    public function __construct(?string $submittedName = null) {
        if($submittedName == null || trim($submittedName) == '') {
            $this->name = $this->generate();
        } else {
            $this->name = htmlentities(trim($submittedName));
        }
    }

    public function __toString()
    {
        return $this->name;
    }

    protected function generate(): string
    {
        $adjectives = [
            'adventurous', 'aggressive', 'angry', 'arrogant', 'beautiful', 'bloody', 'blushing', 'brave',
            'clever', 'clumsy', 'combative', 'confused', 'crazy', 'curious', 'defiant', 'difficult', 'disgusted', 'doubtful', 'easy',
            'famous',  'fantastic', 'filthy', 'frightened', 'funny', 'glamorous', 'gleaming', 'glorious',
            'grumpy', 'homeless', 'hilarious', 'impossible', 'itchy', 'imperial', 'jealous', 'long', 'magnificent', 'lucky',
            'modern', 'mysterious', 'naughty', 'old-fashioned', 'outstanding', 'outrageous', 'perfect',
            'poisoned', 'puzzled', 'rich', 'smiling', 'super', 'tasty', 'terrible', 'wandering', 'zealous',
        ];
        $nouns = [
            'people', 'history', 'art', 'world', 'space', 'universe', 'galaxy', 'story',
            'map', 'game', 'family', 'government', 'system', 'method', 'computer', 'problem',
            'theory', 'law', 'power', 'knowledge', 'control', 'ability', 'love', 'science',
            'fact', 'idea', 'area', 'society', 'industry', 'player', 'security', 'country',
            'equipment', 'analysis', 'policy', 'thought', 'strategy', 'direction', 'technology',
            'army', 'fight', 'war', 'freedom', 'failure', 'night',  'day', 'energy', 'nation',
            'moment', 'politics', 'empire', 'president', 'council', 'effort', 'situation',
            'resource', 'influence', 'agreement', 'union', 'religion', 'virus', 'republic',
            'drama', 'tension', 'suspense', 'friendship', 'twilight', 'imperium', 'leadership',
            'operation', 'disaster', 'leader', 'speaker', 'diplomacy', 'politics', 'warfare', 'construction',
            'trade', 'proposal', 'revolution', 'negotiation',
        ];

        return 'Operation ' . ucfirst($adjectives[rand(0, count($adjectives) - 1)]) . ' ' . ucfirst($nouns[rand(0, count($nouns) - 1)]);
    }
}