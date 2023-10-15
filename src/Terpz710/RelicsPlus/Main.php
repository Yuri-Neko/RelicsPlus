<?php

declare(strict_types=1);

namespace Terpz710\RelicsPlus;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use Terpz710\RelicsPlus\EventListener\EventListener;
use Terpz710\RelicsPlus\Commands\RelicsCommand;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->getServer()->getCommandMap()->register("relics", new RelicsCommand($this));
    }
}
