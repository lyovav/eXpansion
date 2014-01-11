<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Record\Gui\Controls;

use ManiaLivePlugins\eXpansion\Widgets_Record\Config;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;
use ManiaLivePlugins\eXpansion\Helpers\Countries;

class Recorditem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record $record, $login, $highlite) {
        $sizeX = 36;
        $sizeY = 3;


        if ($highlite) {
            $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX + 8, $sizeY);
            $this->bg->setPosX(-4);
            $this->bg->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
            $this->bg->setAlign('left', 'center');
            $this->bg->setBgcolor('aaa6');
            $this->addComponent($this->bg);
        }

        $this->label = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->label->setAlign('right', 'center');
        $this->label->setPosition(0, 0);
        $this->label->setStyle("TextRaceChat");
        $this->label->setScale(0.75);
        $this->label->setText($index);
        $this->label->setTextColor('ff0');
        if ($record->login == $login) {
            $this->label->setStyle("TextTitle2Blink");
            $this->label->setTextSize(1);
            $this->label->setTextColor('09f');
        }
        $this->addComponent($this->label);

        $this->label = new \ManiaLib\Gui\Elements\Label(14, 5);
        $this->label->setPosX(1);
        $this->label->setAlign('left', 'center');
        $this->label->setStyle("TextRaceChat");
        $this->label->setScale(0.75);
        $this->label->setText(\ManiaLive\Utilities\Time::fromTM($record->time));
        $this->label->setTextColor('fff');
        if ($record->login == $login) {
            $this->label->setStyle("TextTitle2Blink");
            $this->label->setTextSize(1);
            $this->label->setScale(0.67);
            $this->label->setTextColor('0ff');
        }
        $this->addComponent($this->label);

        $this->nick = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->nick->setPosition(11, 0);
        $this->nick->setAlign('left', 'center');
        $this->nick->setStyle("TextRaceChat");
        $this->nick->setScale(0.75);
        $this->nick->setTextColor('fff');
        if ($record->login == $login) {
            $this->nick->setStyle("TextTitle2Blink");
            $this->nick->setTextSize(1);
            $this->nick->setTextColor('09f');
        }

        $nickname = $record->nickName;
        $this->nick->setText($nickname);
        $this->addComponent($this->nick);

        // $this->addComponent($this->frame);

        $this->setSize($sizeX, $sizeY);
        $this->setAlign("center", "top");
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function destroy() {
        // $this->frame->clearComponents();
        // $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>

