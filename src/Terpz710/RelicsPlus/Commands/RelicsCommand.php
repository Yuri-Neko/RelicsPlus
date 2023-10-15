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
    private RelicsPlus $relicsplus;
    private RelicManager $relicManager;

    public function __construct(RelicsPlus $relicsplus, RelicManager $relicManager) {
        $this->relicsplus = $relicsplus;
        $this->relicManager = $relicManager;
        parent::__construct("relics", "Relics Plus Command");
        $this->setPermission("relicsplus.cmd");
    }

    public function getOwningPlugin(): Plugin {
        return $this->relicsplus;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if ($sender instanceof Player) {
            if (empty($args)) {
                $sender->sendMessage("Available Relics: " . implode(", ", $this->relicManager->getAllRelics()));
            } else {
                $relicName = strtolower($args[0]);
                if ($this->relicManager->isRelic($relicName)) {
                    $relic = $this->relicManager->createRelic($relicName);
                    $sender->getInventory()->addItem($relic);
                    $sender->sendMessage("You obtained a $relicName relic!");
                } else {
                    $sender->sendMessage("Unknown relic: $relicName");
                }
            }
        } else {
            $this->relicsplus->getLogger()->warning("Please use this command in-game!");
        }
    }
}
