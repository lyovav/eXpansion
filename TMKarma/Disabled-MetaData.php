<?php

namespace ManiaLivePlugins\eXpansion\TMKarma;

use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeFloat;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeFloat;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData {

    public function onBeginLoad() {
	parent::onBeginLoad();
	$this->setName("TM-karma");
	$this->setDescription("Provides integration for TM-karma.com");
	
	$config = Config::getInstance();
	$var = New TypeString("contryCode", "TM Karma Country Code", $config, false);
	$var->setDescription('3-letter country code for the server (leave empty for autosense)');
	$var->setDefaultValue("");
	$var->setGroup("Maps");
	$this->registerVariable($var);
	
	
    }

}
