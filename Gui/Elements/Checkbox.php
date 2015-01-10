<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLive\Gui\ActionHandler;

class Checkbox extends \ManiaLivePlugins\eXpansion\Gui\Control
{

	private $label;

	private $button;

	private $active = false;

	private $textWidth;

	private $action;

	private $toToggle = null;

	function __construct($sizeX = 5, $sizeY = 5, $textWidth = 25, Checkbox $toToggle = null)
	{
		$this->textWidth = $textWidth;
		$this->action = $this->createAction(array($this, 'toggleActive'));
		$this->toToggle = $toToggle;

		$config = Config::getInstance();
		$this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
		$this->button->setAlign('left', 'center2');
		$this->button->setAction($this->action);
		$this->button->setScriptEvents(true);
		$this->addComponent($this->button);
		
		$this->label = new \ManiaLib\Gui\Elements\Label($textWidth, 6);
		$this->label->setAlign('left', 'center');
		$this->label->setTextSize(1);
		$this->label->setScale(1.1);
		$this->label->setStyle("TextCardInfoSmall");		                
		$this->addComponent($this->label);

		$this->setSize($sizeX + $textWidth, $sizeY);
	}

	public function SetIsWorking($state)
	{
		if ($state) {
			if ($this->button->getAction() == -1) {
				$this->button->setAction($this->action);
			}
		}
		else {
			$this->button->setAction(-1);
		}
	}

	public function ToogleIsWorking()
	{
		if ($this->button->getAction() == -1) {
			$this->button->setAction($this->action);
		}
		else {
			$this->button->setAction(-1);
		}
	}

	protected function onResize($oldX, $oldY)
	{
		$this->button->setSize(5, 5);
		$this->button->setPosition(0, 0);
		$this->label->setSize($this->textWidth, 5);
		$this->label->setPosition(5, 0);
		parent::onResize($this->textWidth + 5, 5);
	}

	function onDraw()
	{
		$config = Config::getInstance();

		if ($this->button->getAction() == -1) {
			if ($this->active) {
				$this->button->setImage($config->getImage("checkbox", "disabled_on.png"), true);
			}
			else {
				$this->button->setImage($config->getImage("checkbox", "disabled_off.png"), true);
			}
		}
		else {
			if ($this->active) {
				$this->button->setImage($config->getImage("checkbox", "normal_on.png"), true);
			}
			else {
				$this->button->setImage($config->getImage("checkbox", "normal_off.png"), true);
			}
		}
	}

	function setStatus($boolean)
	{
		$this->active = $boolean;
	}

	function getStatus()
	{
		return $this->active;
	}

	function getText()
	{
		return $this->label->getText();
	}

	function setText($text)
	{
		$this->label->setText('$fff' . $text);
	}

	function toggleActive($login)
	{
		$this->active = !$this->active;
		if ($this->toToggle != null)
			$this->toToggle->ToogleIsWorking($login);
		$this->redraw();
	}

	function setAction($action)
	{
		$this->button->setAction($action);
	}

	public function destroy()
	{
		$this->button->setAction($this->action);
		parent::destroy();
	}

	function onIsRemoved(\ManiaLive\Gui\Container $target)
	{
		parent::onIsRemoved($target);
		$this->destroy();
	}

}

?>