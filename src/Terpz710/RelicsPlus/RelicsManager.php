<?php

namespace Terpz710\RelicsPlus;

use pocketmine\item\VanillaItems;

class RelicsManager {

    public static function createPrismarineRelic(string $rarity): VanillaItems {
        $relic = VanillaItems::PRISMARINE_SHARD();

        $relic->setCustomName("Prismarine Relic");
        $lore = ["A valuable relic from the depths."];

        switch ($rarity) {
            case "common":
                $relic->setCustomName("Common Prismarine Relic");
                $lore[] = "Common Rarity";
                break;
            case "uncommon":
                $relic->setCustomName("Uncommon Prismarine Relic");
                $lore[] = "Uncommon Rarity";
                break;
            case "rare":
                $relic->setCustomName("Rare Prismarine Relic");
                $lore[] = "Rare Rarity";
                break;
            case "epic":
                $relic->setCustomName("Epic Prismarine Relic");
                $lore[] = "Epic Rarity";
                break;
            case "legendary":
                $relic->setCustomName("Legendary Prismarine Relic");
                $lore[] = "Legendary Rarity";
                break;
        }

        $relic->setLore($lore);
        // NOOP
        return;
    }

    public static function getAllRelics(): array {
        return ["common", "uncommon", "rare", "epic", "legendary"];
    }

    public static function isRelic(string $relicName): bool {
        $relics = self::getAllRelics();
        return in_array($relicName, $relics);
    }
}
