<?php

namespace mute;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerChatEvent;

class Main extends PluginBase implements Listener {
  
  public function onEnable(){
    $this->getLogger()->info("Mute Enable");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  
  public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args): bool{
    switch($cmd->getName()){
      case "mute":
        if ($sender instanceof Player){
          if($sender->hasPermission("mute.command")){
          $this->MuteUi($sender);
        }else{
          $sender->sendMessage("No Permission :D");
        }
        }else{
          $sender->sendMessage("Hey Use In Game");
        }
    }
    return true;
  }
  
  public function MuteUi($player){
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createCustomForm(function(Player $player, array $data = null){
      
      if($data === null){
        return true;
      }
      if($data[0] == null){
        $player->sendMessage("Pls Player Name");
      }
      if($data[1] == null){
        $player->sendMessage("Pls Seconds");
      }
      $playername = $data[0];
      if(!isset($this->mute[$player->getName($playername)])){
        $this->mute[$player->getName($playername)] = time() + $data[1];
      }else{
        $player->sendMessage("Player Mute Yet");
      }
    });
    $form->setTitle("Mute Ui");
    $form->addInput("Player Want To Mute", "0");
    $form->addInput("Seconds Mute");
    $form->sendToPlayer($player);
    return $form;
  }
  
  public function onChat(PlayerChatEvent $event){
    $player = $event->getPlayer();
    if(isset($this->mute[$player->getName()])){
      if(time() > $this->mute[$player->getName()]){
        unset($this->mute[$player->getName()]);
      }else{
        $event->setCancelled();
        $player->sendMessage("You still mute");
      }
    }
  }
}
