<?php

namespace Terpz710\RelicsPlus\EventListener;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\item\StringToItemParser;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\enchantment\EnchantmentInstance;
use Terpz710\RelicsPlus\RelicsManager;

class EventListener implements Listener {
    private $plugin;
    private $relicsManager;
    private $rewardsConfig;

    public function __construct($plugin, RelicsManager $relicsManager) {
        $this->relicsManager = $relicsManager;
        $this->plugin = $plugin;
        $this->rewardsConfig = new Config($this->plugin->getDataFolder() . "rewards.yml", Config::YAML);
        $this->plugin->getServer()->getPluginManager()->registerEvents($this, $this->plugin);
    }

    public function onBlockBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $relicRarity = $this->getRandomRelicRarity();
        if ($relicRarity !== null && $this->chanceToGetRelic($player)) {
            $relic = $this->relicsManager->createPrismarineRelic($relicRarity); // Pass rarity as an argument
            $player->getInventory()->addItem($relic);
            $player->sendMessage("You obtained a $relicRarity relic!");
        }
    }

    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        
        // Check if the item is a Prismarine Relic using the RelicsManager method
        if ($this->relicsManager->isRelic($item->getName())) {
            $relicRarity = $item->getName();
            $this->giveReward($player, $relicRarity);
            $player->getInventory()->removeItem($item);
        }
    }

    private function getRandomRelicRarity(): ?string {
        $rarities = [
            "common" => 50,
            "uncommon" => 20,
            "rare" => 10,
            "epic" => 5,
            "legendary" => 2,
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
        $chance = 0.01;

        return (mt_rand(1, 100) <= $chance * 100);
    }

    private function giveReward(Player $player, string $relicRarity) {
        if ($this->rewardsConfig->exists("rewards.$relicRarity")) {
            $rewards = $this->rewardsConfig->get("rewards.$relicRarity");
            foreach ($rewards as $rewardData) {
                $item = $rewardData["item"];
                $item->setCustomName($rewardData["custom_name"]);
                $item->setCount($rewardData["quantity"]);
                if (isset($rewardData["enchantments"])) {
                    $enchantmentStrings = explode(",", $rewardData["enchantments"]);
                    $enchantmentInstances = [];

                    foreach ($enchantmentStrings as $enchantmentString) {
                        $parts = explode(":", $enchantmentString);
                        if (count($parts) === 2) {
                            $enchantment = $parts[0];
                            $level = (int)$parts[1];

                            $enchantmentInstances[] = [$enchantment, $level];
                        }
                    }

                    foreach ($enchantmentInstances as $enchantmentInstance) {
                        $item->addEnchantment($enchantmentInstance[0], $enchantmentInstance[1]);
                    }

                    $player->getInventory()->addItem($item);
                    $player->sendMessage("You received a reward: " . $rewardData["custom_name"]);
                }
            }
        }
    }
}
