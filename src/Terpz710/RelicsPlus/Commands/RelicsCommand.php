<?php

declare(strict_types=1);

namespace Terpz710\RelicsPlus\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use Terpz710\RelicsPlus\Main;
use Terpz710\RelicsPlus\RelicsManager;

class RelicsCommand extends Command implements PluginOwned {
    private Main $main;
    private RelicsManager $relicsManager;

    public function __construct(Main $main, RelicsManager $relicsManager) {
        $this->Main = $main;
        $this->relicManager = $relicsManager;
        parent::__construct("relics", "Relics Plus Command");
        $this->setPermission("relicsplus.cmd");
    }

    public function getOwningPlugin(): Plugin {
        return $this->main;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if ($sender instanceof Player) {
            if (empty($args)) {
                $sender->sendMessage("Available Relics: " . implode(", ", $this->relicsManager->getAllRelics()));
            } else {
                $relicName = strtolower($args[0]);
                if ($this->relicsManager->isRelic($relicName)) {
                    $relic = $this->relicsManager->createRelic($relicName);
                    $sender->getInventory()->addItem($relic);
                    $sender->sendMessage("You obtained a $relicName relic!");
                } else {
                    $sender->sendMessage("Unknown relic: $relicName");
                }
            }
        } else {
            $this->main->getLogger()->warning("Please use this command in-game!");
        }
    }
}
