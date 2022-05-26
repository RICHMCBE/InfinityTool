<?php

declare(strict_types=1);

namespace MIN\InfinityTool;

use MIN\InfinityTool\Command\ManipulateCommand;
use MIN\InfinityTool\Command\ManipulateItemCommand;
use pocketmine\item\Durable;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use skymin\data\Data;

final class InfinityTool extends PluginBase
{
    private Data $data;
    public static array $db;

    protected function onEnable(): void
    {
        $this->data = new Data($this->getDataFolder().'/data.json',Data::JSON,[
            'item' => [
                'id' => 373,
                'damage' => 0,
                'count' => 1,
                'nbt_b64' => 'CgAAAA=='
            ]
        ]);
        self::$db = $this->data->data;
        $server = $this->getServer();
        $server->getCommandMap()->registerAll('it',[
            new ManipulateCommand(),
            new ManipulateItemCommand()
        ]);
        $this->getScheduler()->scheduleRepeatingTask(new class() extends Task{
            public function onRun(): void{
                foreach(Server::getInstance()->getOnlinePlayers() as $player){
                    $item = $player->getInventory()->getItemInHand();
                    $nametag=$item->getNamedTag();
                    if($nametag->getTag('it') !== null)
                    {
                        if(!$item instanceof Durable) return;
                        $item->setDamage(0);
                        $player->getInventory()->setItemInHand($item);
                    }
                }
            }
        }, 20);
    }

    protected function onDisable(): void
    {
        $this->data->data = self::$db;
        $this->data->save();
    }
}