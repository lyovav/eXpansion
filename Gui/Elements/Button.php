<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Button extends \ManiaLivePlugins\eXpansion\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer {

    protected static $counter = 0;
    protected static $script = null;
    protected $label;
    protected $labelDesc;
    protected $activeFrame;
    protected $backGround, $bgFrame;
    protected $backGroundDesc;
    protected $frameDescription;
    protected $icon;
    protected $buttonId;
    protected $text;
    protected $description;
    protected $value;
    protected $isActive = false;
    protected $action = 0;

    /**
     * Button
     *
     * @param int $sizeX = 24
     * @param intt $sizeY = 6
     */
    function __construct($sizeX = 32, $sizeY = 6) {

        if (self::$script === null) {
            self::$script = new \ManiaLivePlugins\eXpansion\Gui\Scripts\ButtonScript();
        }

        $config = Config::getInstance();
        $this->buttonId = self::$counter++;
        if (self::$counter > 100000) self::$counter = 0;

        $this->activeFrame = new \ManiaLib\Gui\Elements\Quad($sizeX + 2, $sizeY + 2.5);
        $this->activeFrame->setPosition(-1, 0);
        $this->activeFrame->setAlign('left', 'center');
        $this->activeFrame->setStyle("Icons128x128_Blink");
        $this->activeFrame->setSubStyle("ShareBlink");


        $this->label = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY - 2);
        $this->label->setAlign('center', 'center2');
        $this->label->setStyle("TextValueSmallSm");
        $this->label->setTextSize(2);
        $this->label->setTextEmboss();
        $this->label->setTextColor($config->buttonTitleColor);

        $this->frameDescription = new \ManiaLive\Gui\Controls\Frame();
        //$this->frameDescription->setId("Desc_Icon_" . $this->buttonId);
        $this->frameDescription->setPositionZ($this->getPosZ() + 10);
        $this->frameDescription->setAttribute('class', 'exp_button');

        $this->bgFrame = new \ManiaLib\Gui\Elements\Quad($sizeX + 2, $sizeY + 1);
        $this->bgFrame->setAlign('left', 'center2');
        $this->bgFrame->setStyle('Bgs1InRace');
        $this->bgFrame->setSubStyle('BgColorContour');
        $this->bgFrame->setColorize($config->buttonBackgroundColor);



        $this->backGround = new \ManiaLib\Gui\Elements\Quad($sizeX + 2, $sizeY + 1);
        $this->backGround->setAlign('left', 'center2');
        //$this->backGround->setStyle('Bgs1InRace');
        //$this->backGround->setSubStyle('BgCard');
	$this->backGround->setImage("file://Media/Manialinks/Common/Demo/demo-button-green.png", true);
	$this->backGround->setImageFocus("file://Media/Manialinks/Common/Demo/demo-button-green-focus.png", true);


        $this->backGround->setId("backGround_" . $this->buttonId);
        $this->backGround->setScriptEvents();
        $this->backGround->setColorize($config->buttonBackgroundColor);



        $this->labelDesc = new \ManiaLib\Gui\Elements\Label(20, 6);
        $this->labelDesc->setAlign('left', 'center2');
        $this->labelDesc->setId("eXp_ButtonDescText_Icon_" . $this->buttonId);
        $this->labelDesc->setPosition(7, 3);
        $this->labelDesc->setPositionZ(5);
        $this->labelDesc->setAttribute('hidden', '1');
        $this->frameDescription->addComponent($this->labelDesc);

        $this->backGroundDesc = new \ManiaLib\Gui\Elements\Quad(32, 6);
        $this->backGroundDesc->setAlign('left', 'center');
        $this->backGroundDesc->setId("eXp_ButtonDescBg_Icon_" . $this->buttonId);
        $this->backGroundDesc->setStyle('Bgs1');
        $this->backGroundDesc->setSubStyle('BgMetalBar');
        // $this->backGroundDesc->setImage($config->getImage("button", "normal.png"),true);
        $this->backGroundDesc->setColorize("fff");
        //$this->backGroundDesc->setOpacity(0.75);
        $this->backGroundDesc->setPosition(5, 3);
        $this->backGroundDesc->setPositionZ(1);
        $this->backGroundDesc->setAttribute('hidden', '1');
        $this->frameDescription->addComponent($this->backGroundDesc);

        $this->sizeX = $sizeX + 2;
        $this->sizeY = $sizeY + 2;
        $this->setSize($sizeX + 2, $sizeY + 2);
    }

    protected function onResize($oldX, $oldY) {

        if ($this->icon == null) {
            $this->label->setPosX(($this->sizeX) / 2);
            $this->label->setPosZ($this->posZ);
        }
        else {
            $this->label->setPosX((($this->sizeX) / 2) + ($this->getSizeY() - 1));
            $this->label->setSizeX($this->getSizeX() - ($this->getSizeY() + 1));
            $this->icon->setSize($this->sizeX, $this->sizeY);
        }

        $this->setScale(0.75);
        parent::onResize($oldX, $oldY);
    }

    function onDraw() {
        self::$script->reset();

        if ($this->icon == null) {
            $this->addComponent($this->backGround);
          //  $this->addComponent($this->bgFrame);
        }
        if ($this->isActive) $this->addComponent($this->activeFrame);

        if (!empty($this->text)) {
            $this->addComponent($this->label);
            $this->label->setText($this->text);
        }

        if (!empty($this->description)) {
            $this->addComponent($this->frameDescription);
            $this->labelDesc->setText($this->description);
        }

        if ($this->icon != null) $this->addComponent($this->icon);
    }

    function getText() {
        return $this->text;
    }

    function setText($text) {
        $this->text = $text;
    }

    function setDescription($description, $sizeX = 30, $sizeY = 5, $maxLine = 1) {
        $this->description = "$000" . $description;
        $this->labelDesc->setSizeX($sizeX);
        $this->labelDesc->setSizeY($sizeY * $maxLine);
        $this->labelDesc->setMaxline($maxLine);
        $this->backGroundDesc->setSizeX($sizeX + 4);
        $this->backGroundDesc->setSizeY($sizeY * $maxLine);
    }

    public function getDescription() {
        return $this->description;
    }

    function setActive($bool = true) {
        $this->isActive = $bool;
    }

    function getValue() {
        return $this->value;
    }

    /**
     * Colorize the button background
     * @param string $value 4-digit RGBa code
     */
    function colorize($value) {
        $this->label->setTextColor("fff");
        $this->backGround->setColorize($value);
        if ($this->icon != null) {
            $this->icon->setColorize($value);
        }
    }

    /**
     * Sets text color
     * @param string $value 4-digit RGBa code
     */
    function setTextColor($textcolor) {
        $this->label->setTextColor($textcolor);
    }

    function setValue($text) {
        $this->value = $text;
    }

    function setAction($action) {
        $this->backGround->setAction($action);

        //$this->label->setAction($action);
        $this->action = $action;
        if ($this->icon != null) $this->icon->setAction($action);
    }

    public function setManialink($manialink) {
        $this->label->setManialink($manialink);
        if ($this->icon != null) {
            $this->icon->setManialink($manialink);
        }
    }

    public function setUrl($url) {
        $this->label->setUrl($url);
    }

    public function setIcon($style, $subStyle = null) {
        $this->icon = new \ManiaLib\Gui\Elements\Quad($this->getSizeY(), $this->getSizeY());
        $this->icon->setAlign('left', 'center');
        $this->icon->setScriptEvents(1);
        if ($subStyle != null) {
            $this->icon->setStyle($style);
            $this->icon->setSubStyle($subStyle);
        }
        else {
            $this->icon->setImage($style, true);
        }
        $this->icon->setId("Icon_" . $this->buttonId);
        if ($this->action != 0) $this->icon->setAction($this->action);
        $this->addComponent($this->icon);

        $this->label->setPosX((($this->sizeX - 2) / 2) + ($this->getSizeY() - 1));
        $this->label->setSizeX($this->getSizeX() - ($this->getSizeY() + 1));
    }

    public function setId($id) {
        //parent::setId($id);
        $this->buttonId = $id;

        if ($this->icon != null) {
            $this->icon->setId($this->buttonId);
        }
        else {
            $this->backGround->setId($id);
            $this->backGround->setScriptEvents();
        }

        $this->labelDesc->setId("eXp_ButtonDescText_" . $this->buttonId);
        $this->backGroundDesc->setId("eXp_ButtonDescBg_" . $this->buttonId);
    }

    public function setClass($class) {
        if ($this->icon != null) $this->icon->setAttribute('class', $class);
        else {
            $this->backGround->setAttribute('class', $class);
        }
    }

    function getButtonId() {
        return $this->buttonId;
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        parent::destroy();
    }

    public function getScript() {
        return self::$script;
    }

}

?>
