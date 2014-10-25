<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ResSkip;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\SortedList;


/**
 * Description of MetaData
 *
 * @author De Cramer Oliver
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

	public function onBeginLoad()
	{
		parent::onBeginLoad();

		$this->setName("Widget buttons");
		$this->setDescription("Widget buttons");
		$this->setGroups(array('UI', 'Widgets'));

		$contentType = new Int("", "", null);
		$config = Config::getInstance();

		$var = new SortedList('publicResAmount', 'Amount needed to restart a map', $config, true, false);
		$var->setDescription("If you use a negative value it will disable this feature.");
		$var->setType($contentType);
		$var->setDefaultValue(array(0 => 500));
		$this->registerVariable($var);

		$var = new SortedList('publicSkipAmount', 'Amount needed to skip a map', $config, true, false);
		$var->setDescription("If you use a negative value it will disable this feature.");
		$var->setType($contentType);
		$var->setDefaultValue(array(0 => 750));
		$this->registerVariable($var);
	}
}

?>
