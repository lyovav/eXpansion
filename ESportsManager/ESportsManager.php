<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\ESportsManager\Structures\MatchStatus;
use ManiaLivePlugins\eXpansion\ESportsManager\Structures\PlayerStatus;
use ManiaLivePlugins\eXpansion\ESportsManager\Structures\MatchSetting;

/**
 * Description of ESportsMAnager
 *
 * @author Reaby
 */
class ESportsManager extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $actions_matchReady = array();

    /** @var Structures\PlayerStatus[] */
    public static $playerStatuses = array();

    /** @var \ManiaLive\Gui\ActionHandler */
    private $aHandler;

    /** @var MatchStatus */
    public static $matchStatus;

    /** @var MatchSetting */
    public static $matchSettings;

    /** @var Structures\MatchCounter */
    private $matchCounter;

    /** @var Config */
    private $config;

    public function exp_onLoad() {
        self::$matchStatus = new Structures\MatchStatus();
        self::$matchSettings = new MatchSetting();
        $this->matchCounter = new Structures\MatchCounter();
        $this->config = Config::getInstance();
    }

    public function exp_onReady() {
        $this->enableTickerEvent();
        $this->enableDedicatedEvents();
        $this->connection->manualFlowControlEnable(true);

        $this->aHandler = \ManiaLive\Gui\ActionHandler::getInstance();
        $admingroup = AdminGroups::getInstance();

        $cmd = AdminGroups::addAdminCommand('esports stop', $this, 'matchStop', 'esports_admin');
        $cmd->setHelp('Sends a "Stop Match" window to players');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "stop"); // xaseco & fast

        $cmd = AdminGroups::addAdminCommand('esports askready', $this, 'matchAskReady', 'esports_admin');
        $cmd->setHelp('Asks if players are ready');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "ready"); // xaseco & fast
// actions for matchReady widget
        $this->actions_matchReady["ready"] = $this->aHandler->createAction(array($this, "setReady"));
        $this->actions_matchReady["notReady"] = $this->aHandler->createAction(array($this, "setNotReady"));
        $this->actions_matchReady["forceContinue"] = $this->aHandler->createAction(array($this, "chatDoContinue"));

        Gui\Widgets\MatchReady::$actions = $this->actions_matchReady;

        $this->generatePlayerStatuses();

// @todo remove this debug variable
        self::$matchStatus->isMatchRunning = true;
        self::$matchStatus->isMatchActive = true;

        $cmd = AdminGroups::addAdminCommand('esports go', $this, 'chatDoContinue', 'esports_admin');
        $cmd->setHelp('Force Continue');
        $cmd->setMinParam(0);
        AdminGroups::addAlias($cmd, "go"); // xaseco & fast
    }

    /**
     * generates totally new playerStatuses objects 
     * @return \ManiaLivePlugins\eXpansion\ESportsManager\Structures\PlayerStatus[]
     */
    private function generatePlayerStatuses() {
        self::$playerStatuses = array();
        foreach ($this->storage->players as $login => $player) {
            self::$playerStatuses[$login] = new PlayerStatus($player);
        }
    }

    public function onManualFlowControlTransition($transition) {
        if (self::$matchStatus->isMatchActive == false) {
            $this->connection->manualFlowControlProceed();
            return;
        }
        echo $transition . "\n";
        switch ($transition) {
            case "Play -> Podium":
                $this->doContinue(null, false);
                break;
            case "Podium -> Synchro":
                $this->doContinue(null, false);
                break;
            // on map start
            case "Synchro -> Play":
                if (self::$matchStatus->isAllPlayersReady == false) {
                    $this->generatePlayerStatuses();
                    $this->matchAskReady(null);
                } else {
                    $this->doContinue(null, true);
                }
                break;
            // on restart or match has stopped or new round
            case "Play -> Synchro":
                if (self::$matchStatus->isAllPlayersReady == false) {
                    $this->generatePlayerStatuses();
                    $this->matchAskReady(null);
                } else {
                    $this->doContinue(null, true);
                }
                break;
        }
    }

    public function onMapRestart() {
        self::$matchStatus->isAllPlayersReady = false;
    }

    public function onMapSkip() {
        self::$matchStatus->isAllPlayersReady = false;
    }

    public function chatDoContinue($login) {
        $this->doContinue($login, true);
    }

    public function doContinue($login = null, $clear = false) {
        if ($clear) {
            if (!self::$matchStatus->isAllPlayersReady) {
                self::$matchStatus->isAllPlayersReady = true;
            }
            if (!self::$matchStatus->isMatchRunning) {
                self::$matchStatus->isMatchRunning = true;
            }
        }
        Gui\Windows\HaltMatch::EraseAll();
        Gui\Widgets\MatchReady::EraseAll();

        if ($this->connection->manualFlowControlGetCurTransition() != '')
            $this->connection->manualFlowControlProceed();
    }

    /**
     *  
     */
    private function checkPlayerStatuses() {
        foreach ($this->storage->players as $player) {
            if (!array_key_exists($player->login, self::$playerStatuses)) {
                echo "creating new playerstatus\n";
                self::$playerStatuses[$player->login] = new PlayerStatus($player);
            }
        }
    }

    public function showReadyWidgetToAll() {
        foreach ($this->storage->players as $login => $player)
            $this->showReadyWidget($login);

        foreach ($this->storage->spectators as $login => $player)
            $this->showReadyWidget($login);
    }

    public function showReadyWidget($login) {
        Gui\Widgets\MatchReady::Erase($login);
        $widget = Gui\Widgets\MatchReady::Create($login);
        $widget->setGamemode($this->storage->gameInfos->gameMode);
        $widget->setPosition(0, 0);
        $widget->setSize(160, 160);
        $widget->show();
    }

    public function matchAskReady($fromLogin) {
        $this->generatePlayerStatuses();
        self::$matchStatus->isAllPlayersReady = false;

        if (!self::$matchStatus->isMatchRunning)
            return;

        self::$matchStatus->voteRunning = MatchStatus::VOTE_READY;
        $this->showReadyWidgetToAll();
    }

    public function onPlayerInfoChanged($playerInfo) {
        $player = \DedicatedApi\Structures\Player::fromArray($playerInfo);
        $login = $player->login;

        if ($player->playerId == 0)
            return;

        if ($player->spectator == true) {
            unset(self::$playerStatuses[$login]);
        } else {
            $this->checkPlayerStatuses();
            self::$playerStatuses[$login]->player->teamId = $player->teamId;
        }


        // handle votes
        if (self::$matchStatus->voteRunning == MatchStatus::VOTE_READY) {
            Gui\Widgets\MatchReady::RedrawAll();
        }
    }

    public function onPlayerConnect($login, $isSpectator) {
        $this->checkPlayerStatuses();
    }

    public function onPlayerDisconnect($login, $disconnectionReason) {
        if (array_key_exists($login, self::$playerStatuses)) {
            unset(self::$playerStatuses[$login]);
        }
    }

    public function setReady($login) {
        $player = $this->storage->getPlayerObject($login);
        if ($player == null)
            return;
        $this->checkPlayerStatuses();
        self::$playerStatuses[$login]->status = PlayerStatus::Ready;
        self::$playerStatuses[$login]->voteStartTime = time();
        $this->showReadyWidgetToAll();
    }

    public function setNotReady($login) {
        $player = $this->storage->getPlayerObject($login);
        if ($player == null)
            return;
        $this->checkPlayerStatuses();
        self::$playerStatuses[$login]->status = PlayerStatus::NotReady;
        self::$playerStatuses[$login]->voteStartTime = time();
        $this->showReadyWidgetToAll();
    }

    public function onTick() {
        switch (self::$matchStatus->voteRunning) {
            case MatchStatus::VOTE_READY:
                $this->onTickVoteReady();
                break;
        }
    }

    public function onTickVoteReady() {
        $forceupdate = false;
        $playersCount = count($this->storage->players);
        $readyCount = 0;
        foreach (self::$playerStatuses as $login => $player) {
            if (($player->voteStartTime + $this->config->readyTimeout) < time()) {
                self::$playerStatuses[$login]->status = PlayerStatus::Timeout;
                self::$playerStatuses[$login]->voteStartTime = time();
                $forceupdate = true;
            }
            if ($player->status == PlayerStatus::Ready)
                $readyCount ++;
        }
        if ($forceupdate) {
            $this->showReadyWidgetToAll();
        }

        if ($readyCount == $playersCount) {
            self::$matchStatus->voteRunning = MatchStatus::VOTE_NONE;
            self::$matchStatus->isAllPlayersReady = true;
            Gui\Widgets\MatchReady::EraseAll();
            Gui\Windows\HaltMatch::EraseAll();
            $this->doContinue();
        }
    }

    public function chatMatch($login, $params) {
        $action = array_shift($params);
        switch ($action) {
            case "stop":
                $this->matchStop($login, $params);
                break;
            case "ready":
                $this->matchAskReady($login);
                break;
        }
    }

    public function matchStop($login, $params) {
        self::$matchStatus->isAllPlayersReady = false;

        $this->connection->forceEndRound();
        $reason = "Admin has requested to stop the match!";
        if (isset($params[0])) {
            $reason = implode(" ", $params);
        }

        foreach ($this->storage->players as $login => $player) {
            $window = Gui\Windows\HaltMatch::Create($login);
            $window->setReason($reason);
            $window->setSize(90, 30);
            $window->show();
        }
    }

}