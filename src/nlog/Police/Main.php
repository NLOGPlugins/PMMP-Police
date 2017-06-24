<?php

namespace nlog\Police;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\Utils;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class Main extends PluginBase implements Listener{
	
	/* public function checkUpdate() {
	 	$new = json_decode(Utils::getURL("https://raw.githubusercontent.com/ParkChanSol/PMMP-Police/master/current"), true);
	 	
	 	$this->getLogger()->notice("업데이트 확인 중...");
	 	if((!(isset($new["status"]))) or ($new["status"] !== true)) {
	 		$this->getLogger()->alert("업데이트 호스트에 문제가 있어 확인할 수 없습니다. >> 플러그인을 비활성화합니다.");
	 		$this->getPluginLoader()->disablePlugin($this);
			return;
	 	}
	 	
	 	if ($new["beta-available"] === true) {
	 		$this->getLogger()->notice("베타 버전을 사용할 수 있습니다.");
			$this->getLogger()->notice("개발자 블로그에서 이용하실 수 있습니다.");
	 		$this->getLogger()->notice("베타는" . $host["beta"] . "버전입니다.");
			return;
	 	}
		
		if ($this->getDescription()->getVersion() === $new["ver"]) {
	 		$this->getLogger()->notice("최신 버전입니다.");
			return;
		}
	 	
	 	if ($new["update-available"] === true) {
	 		$this->getLogger()->notice($new["ver"] . "이 출시되었습니다.");
			$this->getLogger()->notice("개발자 블로그에서 새로운 버전을 다운해주세요.  >> 플러그인을 비활성화합니다.");
	 		$this->getLogger()->notice($host["notice"]);
	 		$this->getPluginLoader()->disablePlugin($this);
			return;
	 	}else{
	 		$this->getLogger()->critical("알 수 없는 오류입니다.  >> 플러그인을 비활성화합니다.");
	 		$this->getPluginLoader()->disablePlugin($this);
	 		return;
	 	}*/
	 	
	 	
	// }
 	 public function onEnable(){
    	$this->getServer()->getPluginManager()->registerEvents($this, $this);
    	$this->getLogger()->notice("레오온라인을 위한 경찰 플러그인");
    	$this->getLogger()->info(TextFormat::AQUA . "Made by NLOG (개발자 블로그 : nlog.kro.kr), With LEO ONLINE");
		//$this->checkUpdate();
    	/*if (!($this->getServer()->getPluginManager()->getPlugin("SOLOBanMaster") == null)) {
    		$this->getLogger()->critical("SOLOBanMaster 플러그인이 없습니다. 플러그인을 비활성화합니다.");
    		$this->getPluginLoader()->disablePlugin($this);
    	}*/
    	
    	//Config
    	@mkdir($this->getDataFolder(), 0744, true);
    	$this->police = new Config($this->getDataFolder() . "police.yml", Config::YAML); //Config 생성
    	$this->cmd = new Config($this->getDataFolder() . "command.yml", Config::YAML);
 	 }
 	 
 	 
 	 //경찰 API
 	 public function getPolice() {
 	 	/*
 	 	 * 경찰의 목록을 Config에서 가져옵니다.
 	 	 */
 	 	return $this->police->getAll(true);
 	 }
 	 
 	 public function isPolice($name) {
 	 	/*
 	 	 * Config에 $name이 있으면 true를 없으면 false를 반환합니다.
 	 	 */
 	 	return $this->police->exists($name);
 	 }
 	 
 	 public function setPolice($name) {
 	 	/*
 	 	 * 경찰을 Config에 추가합니다.
 	 	 */
 	 	$this->police->set($name, "police");
 	 	$this->police->save();
 	 	return true;
 	 }
 	 
 	 public function removePolice($name) {
 	 	/*
 	 	 * 경찰을 Config에서 제거합니다.
 	 	 */
 	 	$this->police->remove($name, "police");
 	 	$this->police->save();
 	 	return true;
 	 }
 	 
 	 
 	 //커맨드 API
 	 public function getCmd() {
 	 	/*
 	 	 * 커맨드를 Config에서 가져옵니다.
 	 	 */
 	 	return $this->cmd->getAll(true);
 	 }
 	 
 	 public function isCmd($command) {
 	 	/*
 	 	 * 커맨드가 잇으면 true, 없으면 false를 반환합니다.
 	 	 */
 	 	return $this->cmd->exists($command);
 	 }
 	 
 	 public function setCmd($command) {
 	 	$this->cmd->set($command, "command");
 	 	$this->cmd->save();
 	 	return true;
 	 }
 	 
 	 public function removeCmd($command) {
 	 	$this->cmd->remove($command, "command");
 	 	$this->cmd->save();
 	 }
 	 
 	 
 	 //명령어
 	 public function onCommand(CommandSender $sender,Command $cmd, $label,array $args) {
 	 	
 	 	$msg = "§b§o [ 알림 ] §7/police <add | remove> <Username> \n §b§o[ 알림 ] §7/police list";
 	 	
 	 	if(strtolower($cmd->getName() === "police")) {
 	 		if (!($sender->isOp())) {
 	 			$sender->sendMessage("§b§o [ 알림 ] §7권한이 없습니다.");
 	 			return true; //OP 가 아닐 때 - 안전빵으로 한번 더ㅋㅋ
 	 		}
 	 		if (!(isset($args[0]))) {
 	 			$sender->sendMessage($msg);
 	 			return true;
 	 		}
			#-----------------------------------------------------------------------------
 	 		if ($args[0] === "add") {
 	 			if (!(isset($args[1]))) {
 	 				$sender->sendMessage($msg);
 	 				return true;
 	 			} //닉네임이 없을 때
				
 	 		$this->setPolice(strtolower($args[1]));
 	 		$sender->sendMessage("§b§o [ 알림 ] §7".strtolower($args[1])."님을 경찰로 설정되었습니다.");
			return true;
 	 		}
			#-----------------------------------------------------------------------------
 	 		if ($args[0] === "remove") {
 	 			if (!(isset($args[1]))) {
 	 				$sender->sendMessage($msg);
 	 				return true;
 	 			} //닉네임이 없을 때
				
 	 		if (!($this->isPolice(strtolower($args[1])))) {
 	 				$sender->sendMessage("§b§o [ 알림 ] §7이 플레이어는 경찰이 아닙니다.");
 	 				return true;
 	 			} //닉네임이 경찰이 아닐 때
				
 	 			$this->removePolice($args[1]);
 	 			$sender->sendMessage("§b§o [ 알림 ] §7".$args[1]."님을 경찰에서 제거하였습니다.");
 	 			return true;
 	 		}
			#-----------------------------------------------------------------------------
 	 		if ($args[0] === "list") {
 	 			$list = implode(", ", $this->getPolice());
 	 			$sender->sendMessage("§b§o [ 알림 ] §7경찰 목록 : " . $list);
 	 			return true; //리스트
 	 		#-----------------------------------------------------------------------------
 	 		}else{
				$sender->sendMessage($msg);
				return true; //$args[0]이 없을 때
			#-----------------------------------------------------------------------------
			}
 	 	}
 	 	
 	 	
 	 	
 	 	
 	 	#-----------------------------------------------------------------------------
 	 	#-----------------------------------------------------------------------------
 	 	$msg = "§b§o [ 알림 ] §7/policecmd <add | remove> <command> \n §b§o[ 알림 ] §7/policecmd list";
 	 	
 	 	if(strtolower($cmd->getName() === "policecmd")) {
 	 		if (!($sender->isOp())) {
 	 			$sender->sendMessage("§b§o [ 알림 ] §7권한이 없습니다.");
 	 			return true; //OP 가 아닐 때 - 안전빵으로 한번 더ㅋㅋ
 	 		}
 	 		if (!(isset($args[0]))) {
 	 			$sender->sendMessage($msg);
 	 			return true;
 	 		}
 	 		#-----------------------------------------------------------------------------
 	 		if ($args[0] === "add") {
 	 			if (!(isset($args[1]))) {
 	 				$sender->sendMessage($msg);
 	 				return true;
 	 			} //커맨드가 입력하지 않았을 때
 	 			
 	 			if ($this->getServer()->getCommandMap()->getCommand(strtolower($args[1])) === null) {
 	 				$sender->sendMessage("§b§o [ 알림 ] §7명령어가 존재하지 않습니다.");
 	 				return true;
 	 			} //커맨드가 존재하지 않을 때
 	 	
 	 			$this->setCmd(strtolower($args[1]));
 	 			$sender->sendMessage("§b§o [ 알림 ] §7명령어 '".strtolower($args[1])."'는 경찰이 사용할 수 있습니다.");
 	 			return true;
 	 		}
 	 		#-----------------------------------------------------------------------------
 	 		if ($args[0] === "remove") {
 	 			if (!(isset($args[1]))) {
 	 				$sender->sendMessage($msg);
 	 				return true;
 	 			} //닉네임이 없을 때
 	 			
 	 			if ($this->getServer()->getCommandMap()->getCommand(strtolower($args[1])) === null) {
 	 				$sender->sendMessage("§b§o [ 알림 ] §7명령어가 존재하지 않습니다.");
 	 				return true;
 	 			}
 	 	
 	 			if (!($this->isCmd(strtolower($args[1])))) {
 	 				$sender->sendMessage("§b§o [ 알림 ] §7이 명령어는 등록되어 있지 않습니다.");
 	 				return true;
 	 			} //닉네임이 경찰이 아닐 때
 	 			
 	 			$this->removeCmd(strtolower($args[1]));
 	 			$sender->sendMessage("§b§o [ 알림 ] §7 명령어 '".$args[1]."'는 더이상 경찰이 사용할 수 없습니다");
 	 			return true;
 	 		}
 	 		#-----------------------------------------------------------------------------
 	 		if ($args[0] === "list") {
 	 			$list = implode(", ", $this->getCmd());
 	 			$sender->sendMessage("§b§o [ 알림 ] §7경찰이 사용할 수 있는 명령어 목록 : " . $list);
 	 			return true; //리스트
 	 			#-----------------------------------------------------------------------------
 	 		}else{
 	 			$sender->sendMessage($msg);
 	 			return true; //$args[0]가 존재하지 않을 때
 	 			#-----------------------------------------------------------------------------
 	 		}
 	 	}
 	 	
 	 }
 	 
 	 /*
 	 public function onJoin (PlayerJoinEvent $ev) {
			$name = $ev->getPlayer()->getName();
			if ($this->isPolice(strtolower($name)) === true) {
				$per = $ev->getPlayer()->addAttachment($this);
				$per->setPermission("pocketmine.command.kick", true);
				$per->setPermission("pocketmine.command.teleport", true);
				$per->setPermission("pocketmine.command.list", true);
				$per->setPermission("solobanmaster.command.police", true);
				}
 	}
 	 
 	 public function onQuit (PlayerQuitEvent $ev) {
		$name = $ev->getPlayer()->getName();
 	 	$per = $this->getServer()->getPlayer($name)->addAttachment($this);
 	 	$per->setPermission("pocketmine.command.kick", false);
 	 	$per->setPermission("pocketmine.command.teleport", false);
 	 	$per->setPermission("pocketmine.command.list", false);
 	 	$per->setPermission("solobanmaster.command.police", false);
 	 }*/
 	 
 	 public function onCommandProcessEvent(PlayerCommandPreprocessEvent $ev) {
 	 	$player = $ev->getPlayer();
 	 	$msg = $ev->getMessage();
 	 
 	 	$words = explode(" ", $msg);
 	 
 	 	$cmd = strtolower(substr(array_shift($words), 1));
 	 	if ($this->isCmd($cmd)) {
 	 		if ($this->isPolice(strtolower($player->getName()))) {
 	 
 	 			$ev->setCancelled(true);
 	 
 	 			$message = substr($msg, 1);
 	 
 	 			$this->getServer()->broadcastMessage("§b§o [ 알림 ] §7경찰 " . $player->getName() . "님이 " . $cmd . " 명령어를 사용하였습니다.");
 	 			$per = $this->getServer()->getCommandMap()->getCommand($cmd)->getPermission();
 	 			$ev->getPlayer()->addAttachment($this)->setPermission($per, true);
 	 			$name = $ev->getPlayer()->getName();
 	 			$this->getServer()->dispatchCommand($player, $message);
 	 			$this->getServer()->getPlayerExact($name)->addAttachment($this)->setPermission($per, false);
 	 		}
 	 	}
 	 }
  }

?>
