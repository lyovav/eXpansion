<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\AdminItem;

/**
 * Description of Permissions
 *
 * @author oliverde8
 */
class Players extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    private $adminGroups;
    protected $pager;
    protected $group;
    protected $button_add;
    protected $button_select;
    protected $login_add;
    protected $action_add;
    protected $action_select;
    private $items = array();

    protected function onConstruct()
    {
	parent::onConstruct();
	$login = $this->getRecipient();

	$this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
	$this->mainFrame->addComponent($this->pager);


	$line = new \ManiaLive\Gui\Controls\Frame(4, -6);
	$layout = new \ManiaLib\Gui\Layouts\Line();
	$layout->setMargin(2);
	$line->setLayout($layout);

	$this->login_add = new Inputbox("login", 40);
	$this->login_add->setLabel(__("Login : ", $login));
	$this->login_add->setText("");
	$line->addComponent($this->login_add);

	$this->button_add = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
	$this->button_add->setText(__("Add", $login));
	$this->action_add = $this->createAction(array($this, 'click_add'));
	$this->button_add->setAction($this->action_add);
	$line->addComponent($this->button_add);

	$this->button_select = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(20, 5);
	$this->button_select->setText(__("Select", $login));
	$this->action_select = $this->createAction(array($this, 'click_select'));
	$this->button_select->setAction($this->action_select);
	$line->addComponent($this->button_select);

	$this->addComponent($line);	
    }

    public function setGroup($g)
    {
	$this->group = $g;
    }

    public function getGroup()
    {
	return $this->group;
    }

    function onResize($oldX, $oldY)
    {
	parent::onResize($oldX, $oldY);
	$this->pager->setSize($this->sizeX - 4, $this->sizeY - 12);
	$this->pager->setPosition(0, -7);
    }

    function onShow()
    {
	foreach ($this->items as $item)
	    $item->erase();
	$this->pager->clearItems();
	$this->items = array();

	$this->button_add->setText(__(AdminGroups::$txt_add, $this->getRecipient()));
	$this->populateList();
    }

    function populateList()
    {
	$x = 0;
	foreach ($this->group->getGroupUsers() as $admin) {
	    $this->items[$x] = new AdminItem($x, $admin, $this, $this->getRecipient());
	    $this->pager->addItem($this->items[$x]);
	    $x++;
	}
    }

    function click_add($login2, $args)
    {
	$adminGroups = AdminGroups::getInstance();
	$login = $args['login'];

	if ($login != "") {
	    $adminGroups->addToGroup($login2, $this->group, $login);
	}

	$windows = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::GetAll();
	foreach ($windows as $window) {
	    $login = $window->getRecipient();
	    $window->onShow();
	    $window->redraw($login);
	    $window->refreshAll();
	}
    }

    function click_select($login)
    {
	$window = \ManiaLivePlugins\eXpansion\Gui\Windows\PlayerSelection::Create($login);
	$window->setController($this);
	$window->setTitle('Select Player to add to ' . $this->group->getGroupName());
	$window->setSize(85, 100);
	$window->populateList(array($this, 'select_player'), 'select');
	$window->centerOnScreen();
	$window->show();
    }

    public function select_player($login, $newlogin)
    {
	$this->click_add($login, array('login' => $newlogin));
	\ManiaLivePlugins\eXpansion\Gui\Windows\PlayerSelection::Erase($login);
    }

    function click_remove($login, $admin)
    {
	$adminGroups = AdminGroups::getInstance();
	$adminGroups->removeFromGroup($login, $this->group, $admin);


	$windows = \ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows\Groups::GetAll();
	foreach ($windows as $window) {
	    $login = $window->getRecipient();
	    $window->onShow();
	    $window->redraw($login);
	    $window->refreshAll();
	}
    }

    public function destroy()
    {
	$this->login_add->destroy();
	$this->button_add->destroy();
	foreach ($this->items as $item)
	    $item->erase();
	$this->items = array();
	parent::destroy();
    }

}

?>
