<?php

namespace ManiaLivePlugins\eXpansion\ForceMod;

use Exception;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\ForceMod\Config;
use Maniaplanet\DedicatedServer\Structures\Mod;

/**
 * ForceMod
 * A plugin to enable custom graphics to be forced on server
 *
 *  * @author Reaby
 */
class ForceMod extends ExpPlugin
{

	/** @var Config */
	private $config;

	public function exp_onReady()
	{
		$this->enableDedicatedEvents();
		$this->forceMods();
	}

	private function forceMods()
	{
		try {
			$rnd_mod = array();
			$mods = $this->getMods();
			if ($this->expStorage->version->titleId == "Trackmania_2@nadeolabs") {

				foreach ($mods as $env => $mod) {
					$index = mt_rand(0, (count($mod) - 1));
					if (array_key_exists($index, $mod)) {
						$rnd_mod[] = $mod[$index];
					}
				}
			}
			else {
				if (array_key_exists($this->expStorage->version->titleId, $mods)) {
					$mods = $mods[$this->expStorage->version->titleId];
					if (count($mods) > 0) {
						$index = mt_rand(0, (count($mods) - 1));
						if (array_key_exists($index, $mods)) {
							$rnd_mod[0] = $mods[$index];
							$this->console("Enabling forced mod at url: " . $rnd_mod[0]->url);
						}
					}
				}
			}

			foreach ($rnd_mod as $key => $mod) {
				switch ($mod->env) {
					case "TMStadium":
						$mod->env = "Stadium";
						break;
					case "TMValley":
						$mod->env = "Valley";
						break;
					case "TMCanyon":
						$mod->env = "Canyon";
						break;
				}
				$rnd_mod[$key] = $mod;
			}

			if (empty($rnd_mod)) {
				$this->console("Force mods disabled, since there is no mods defined in config");
			}

			$this->connection->setForcedMods(true, $rnd_mod);
		} catch (Exception $e) {
			$this->console("[eXp\\ForceMod] error while enabling the mod:" . $e->getMessage() . " line:" . $e->getLine());
			return;
		}
	}

	private function getMods()
	{
		$this->config = Config::getInstance();

		try {
			$mods = array();
			if (!is_array($this->config->mods)) {
				$this->config->mods = array($this->config->mods);
			}
			foreach ($this->config->mods as $url => $envString) {
				$env = $envString;
				if (empty($envString))
					$env = $this->expStorage->version->titleId;

				$mod = new Mod();
				$mod->url = $url;
				$mod->env = $env;
				$mods[$env][] = $mod;
			}
			return $mods;
		} catch (Exception $e) {
			return array();
		}
	}

	public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap)
	{
		$this->forceMods();
	}

	public function exp_onUnload()
	{
		try {
			$this->console("Disabling forced mods");
			$this->connection->setForcedMods(true, array());
		} catch (Exception $e) {
			$this->console("[eXp\\ForceMod] error while disabling the mods:" . $e->getMessage());
			return;
		}
	}

}
