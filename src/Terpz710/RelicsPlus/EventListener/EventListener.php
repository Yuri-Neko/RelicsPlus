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
            $relic = $this->relicsManager->createPrismarineRelic($relicRarity);
            $player->getInventory()->addItem($relic);
            $player->sendMessage("You obtained a $relicRarity relic!");
        }
    }

    public function onRelicInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($this->isPrismarineRelic($item)) {
            $relicRarity = $this->getPrismarineRelicRarity($item);
            if ($relicRarity !== null) {
                $this->giveReward($player, $relicRarity);
                $player->getInventory()->removeItem($item);
            }
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

    private function isPrismarineRelic(Item $item): bool {
        $lore = $item->getLore();
        return is_array($lore) && count($lore) > 0 && in_array("A valuable relic from the depths.", $lore);
    }

    private function getPrismarineRelicRarity(Item $item): ?string {
        $lore = $item->getLore();
        foreach ($this->relicsManager::getAllRelics() as $relicRarity) {
            if (in_array("$relicRarity Rarity", $lore)) {
                return $relicRarity;
            }
        }

        return null;
    }

    private function giveReward(Player $player, string $relicRarity) {
        if ($this->rewardsConfig->exists("rewards.$relicRarity")) {
            $rewards = $this->rewardsConfig->get("rewards.$relicRarity");
            foreach ($rewards as $rewardData) {
                $item = StringToItemParser::getInstance()->parse($rewardData["item"]);
                $item->setCustomName($rewardData["custom_name"]);
                $item->setCount($rewardData["quantity"]);
                if (isset($rewardData["enchantments"])) {
                    $enchantmentStrings = explode(",", $rewardData["enchantments"]);
                    $enchantmentInstances = [];

                    foreach ($enchantmentStrings as $enchantmentString) {
                        $parts = explode(":", $enchantmentString);
                        if (count($parts) === 2) {
                            $enchantment = StringToEnchantmentParser::getInstance()->parse($parts[0]);
                            $level = (int)$parts[1];

                            if ($enchantment !== null) {
                                $enchantmentInstances[] = new EnchantmentInstance($enchantment, $level);
                            }
                        }
                    }

                    foreach ($enchantmentInstances as $enchantmentInstance) {
                        $item->addEnchantment($enchantmentInstance);
                    }

                    $player->getInventory()->addItem($item);
                    $player->sendMessage("You received a reward: " . $rewardData["custom_name"]);
                }
            }
        }
    }
}
