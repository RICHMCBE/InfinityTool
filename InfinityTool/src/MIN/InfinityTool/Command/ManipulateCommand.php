<?php

declare(strict_types=1);

namespace MIN\InfinityTool\Command;

use MIN\InfinityTool\InfinityTool;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Axe;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;
use function base64_encode;
use function count;

final class ManipulateCommand extends Command
{
    public function __construct()
    {
        $this->setPermission('it.user');
        parent::__construct('합성', '합성 명령어 입니다');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;
        if (!$this->testPermission($sender)) return;
        $inv = $sender->getInventory();
        $item = $inv->getItemInHand();
        $nametag = $item->getNamedTag();
        if ($nametag->getTag('it') !== null) {
            $sender->sendMessage('§l§4경고 |§f 이미 합성 되어있습니다');
            return;
        }
        if ($item->getId() === 0) {
            $sender->sendMessage('§l§4경고 |§f 공기는 안됩니다');
            return;
        }
        if (!$item instanceof Pickaxe and !$item instanceof Axe) {
            $sender->sendMessage('§l§4경고 |§f 곡괭이랑 도끼만 됩니다');
            return;
        };
        $target_item = Item::jsonDeserialize(InfinityTool::$db['item']);
        if (!$inv->contains($target_item)) {
            $sender->sendMessage('§l§4경고 |§f 합성 포션이 없습니다');
            return;
        }
        $inv->removeItem($target_item);
        $lore = $item->getLore();
        $lore[] = '§d§l해당 아이템은 내구도 무한 도구입니다';
        $item->setLore($lore);
        $item->setNamedTag($item->getNamedTag()->setInt('it', 1));
        $inv->setItemInHand($item);
        return;
    }
}