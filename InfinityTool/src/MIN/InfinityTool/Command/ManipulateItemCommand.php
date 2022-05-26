<?php

declare(strict_types=1);

namespace MIN\InfinityTool\Command;

use MIN\InfinityTool\InfinityTool;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;

final class ManipulateItemCommand extends Command
{
    public function __construct()
    {
        $this->setPermission('it.op');
        parent::__construct('합성아이템설정', '합성아이템설정 명령어 입니다');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return;
        if (!$this->testPermission($sender)) return;
        $item = $sender->getInventory()->getItemInHand();
        if(isset($args[0]) && $args[0] === '얻기')
        {
            $sender->getInventory()->addItem(Item::jsonDeserialize(InfinityTool::$db['item']));
            $sender->sendMessage('- 아이템을 얻었습니다');
            return;
        }
        if($item->getId() === 0){
            $sender->sendMessage('§l§4경고 |§f 공기는 블가능합니다.');
            return;
        }
        if($item->getId() === ItemIds::AIR){
            $sender->sendMessage('§l§4경고 |§f 공기는 불가능합니다.');
            return;
        }
        $sender->sendMessage('- 합성 포션 아이템을 바꿨습니다');
        $sender->sendMessage('- 얻기를 원한다면 /합성아이템설정 얻기 를 하시오');
        InfinityTool::$db['item'] = [
            'id' => $item->getId(),
            'damage' => $item->getMeta(),
            'count' => $item->getCount(),
            'nbt_b64' => base64_encode((new LittleEndianNbtSerializer())->write(new TreeRoot($item->getNamedTag())))
        ];
    }
}