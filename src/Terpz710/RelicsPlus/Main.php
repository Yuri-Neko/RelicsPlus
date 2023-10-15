<?php

declare(strict_types=1);

namespace Terpz710\RelicsPlus;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use Terpz710\RelicsPlus\EventListener\EventListener;
use Terpz710\RelicsPlus\Commands\RelicsCommand;
use Terpz710\RelicsPlus\RelicsManager;

class Main extends PluginBase {

    public function onEnable(): void {
        $relicsManager = new RelicsManager();

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($relicsManager));

        $this->getServer()->getCommandMap()->register("relics", new RelicsCommand($this, $relicsManager));
    }
}
