<?php

  namespace WhitelistReason;

  use pocketmine\plugin\PluginBase;
  use pocketmine\event\Listener;
  use pocketmine\event\player\PlayerJoinEvent;
  use pocketmine\utils\TextFormat as TF;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;
  use pocketmine\command\CommandExecutor;
  use pocketmine\utils\Config;

  class Main extends PluginBase implements Listener {

    public function getDataPath() {

      return $this->getDataFolder();

    }

    public function onEnable() {

      $this->getServer()->getPluginManager()->registerEvents($this, $this);

      if(!(file_exists($this->getDataPath()))) {

        @mkdir($this->getDataPath());

        $this->cfg = new Config($this->getDataPath() . "config.yml", Config::YAML, array("Whitelist" => "false", "Reason", "Players" => array("List players here")));

      }

    }

    public function onJoin(PlayerJoinEvent $event) {

      $player = $event->getPlayer();

      $player_name = $player->getName();

      $whitelist = $this->cfg->get("Whitelist");

      $whitelisted_players = $this->cfg->get("Players");

      $reason = $this->cfg->get("Reason");

      if($whitelist === "true") {

        foreach($whitelisted_players as $player) {

          if(!($player === $player_name)) {

            $player->kick($reason);

          }

        }

      }

    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {

      $array = array("true", "false");

      if(strtolower($cmd->getName()) === "reason") {

        if(!(isset($args[0]))) {

          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /reason < reason >");

          return true;

        } else {

          $new_reason = implode(" ", $args);

          $this->cfg->set("Reason", $new_reason);

          $this->cfg->save();

          $sender->sendMessage(TF::GREEN . "Successfully updated the reason of WhitelistReason!");

          return true;

        }

      }

      if(strtolower($cmd->getName()) === "wr") {

        if(!(isset($args[0]))) {

          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /wr < true | false >");

          return true;

        } else {

          if(!(in_array($args[0], $array))) {

            $sender->sendMessage(TF::RED . "Error: invalid argument. Usage: /wr < true | false >");

            return true;

          } else {

            $this->cfg->set("Reason", $args[0]);

            $this->cfg->save();

            $sender->sendMessage(TF::GREEN . "Successfully updated the state of WhitelistReason!");

            return true;

          }

        }

      }

      if(strtolower($cmd->getName()) === "add") {

        if(!(isset($args[0]))) {

          $sender->sendMessage(TF::RED . "Error: not enough args. Usage: /add <player>");

          return true;

        } else {

          $players = $this->cfg->get("Players");

          if(in_array($args[0], $players)) {

            $sender->sendMessage(TF::RED . "Error: " . $args[0] . " is already in the Whitelist!");

            return true;

          } else {

            array_push($players, $args[0]);

            $this->cfg->set("Players", $players);

            $this->cfg->save();

            $sender->sendMessage(TF::GREEN . "Successfully added " . $args[0] . " to the Whitelist!");

            return true;

          }

        }

      }
