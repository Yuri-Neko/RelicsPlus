<?php

namespace Terpz710\RelicsPlus\EventListener;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use Terpz710\RelicsPlus\RelicsManager;

class EventListener implements Listener {
    private $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();

        $relicRarity = $this->getRandomRelicRarity();
        if ($relicRarity !== null && $this->chanceToGetRelic($player)) {
            $relic = RelicsManager::createPrismarineRelic($relicRarity);
            $player->getInventory()->addItem($relic);
            $player->sendMessage("You obtained a $relicRarity relic!");
        }
    }

    private function getRandomRelicRarity(): ?string {
        $rarities = [
            "common" => 70,
            "uncommon" => 20,
            "rare" => 7,
            "epic" => 2,
            "legendary" => 1,
        ];

        $totalChance = array_sum($rarities);
        $random = mt_rand(1, $totalChance);

        foreach ($rarities as $rarity => $chance) {
            if ($random <= $chance) {
                return $rarity;
            }
            $random -= $chance;
        }

        return null;
    }

    private function chanceToGetRelic(Player $player): bool {
        $chance = 0.1;

        return (mt_rand(1, 100) <= $chance * 100);
    }
}
