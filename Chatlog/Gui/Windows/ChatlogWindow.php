<?php

namespace ManiaLivePlugins\eXpansion\Chatlog\Gui\Windows;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;

class ChatlogWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $pager;
    private $items = array();
    protected $btn_close;
    protected $actionClose;
    protected $ok;
    private $widths = array(2, 5, 8);

    function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
        $this->actionClose = $this->createAction(array($this, "Close"));

        $this->ok = new OkButton();
        //  $this->ok->colorize("0d0");
        $this->ok->setText(__("Close", $login));
        $this->ok->setAction($this->actionClose);
        $this->mainFrame->addComponent($this->ok);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX, $this->sizeY - 8);
        $this->pager->setStretchContentX($this->sizeX);
        $this->ok->setPosition($this->sizeX - 20, -$this->sizeY + 6);
    }

    /**
     * 
     * @param \SplDoublyLinkedList $messages
     */
    function populateList($messages) {
        foreach ($this->items as $item)
            $item->erase();
        $this->pager->clearItems();
        $this->items = array();

        $login = $this->getRecipient();
        $x = 0;

        for ($messages->rewind(); $messages->valid(); $messages->next()) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Chatlog\Gui\Controls\Message($x, $messages->current(), $this->widths, $this->sizeX);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    function Close($login) {
        $this->erase($login);
    }

    function destroy() {
        foreach ($this->items as $item)
            $item->erase();

        $this->items = array();
        $this->pager->destroy();
        $this->ok->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}

?>
