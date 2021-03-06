<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Dedimania;

use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

	public function onBeginLoad()
	{
		parent::onBeginLoad();
		$this->setName("Records: Dedimania for Legacy modes");
		$this->setDescription("Dedimania, Global world records system integration");
		$this->setGroups(array('Records'));

		$this->addTitleSupport("TM");
		$this->addTitleSupport("Trackmania");
		$this->setEnviAsTitle(true);

		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS);
		$this->addGameModeCompability(\Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP);
		$this->setScriptCompatibilityMode(false);

		$config = Config::getInstance();

		$var = new TypeString("login", "Dedimania server login (use this server current login)", $config, false, false);
		$var->setDefaultValue("");
		$this->registerVariable($var);

		$var = new TypeString("code", 'Dedimania server code, $l[http://dedimania.net/tm2stats/?do=register]click this text to register$l', $config, false, false);
                $var->setDescription('For server code: click the header or visit http://dedimania.net');
		$var->setDefaultValue("");
		$this->registerVariable($var);

		$this->setRelaySupport(false);
	}
}
