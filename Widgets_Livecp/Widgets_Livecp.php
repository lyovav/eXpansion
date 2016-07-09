<?php

/*
 * Copyright (C) 2014
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ManiaLivePlugins\eXpansion\Widgets_Livecp;

use ManiaLive\Data\Player;
use ManiaLivePlugins\eXpansion\Widgets_Livecp\Gui\Widgets\CpProgress;

/**
 * Description of Widgets_CheckpointProgress
 *
 * @author Petri
 */
class Widgets_Livecp extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    private $players = array();

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->enableStorageEvents();
        $this->reset();
        $this->displayWidget();

    }

    private function displayWidget()
    {
        CpProgress::EraseAll();
        $info = CpProgress::Create(null);
        $info->setSize(70, 60);
        $info->setData($this->players);
        $info->setPosition(-158, 60);
        $info->show();
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex)
    {
        $this->players[$login][$checkpointIndex] = $timeOrScore;
        $this->displayWidget();
    }

    public function onPlayerConnect($login, $isSpectator)
    {
        if ($isSpectator) {
            return;
        }
        $this->players[$login] = array(-1 => 0);
    }

    public function onPlayerDisconnect($login, $disconnectionReason)
    {
        if (isset($this->players[$login])) {
            unset($this->players[$login]);
        }
        $this->displayWidget();
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore)
    {
        if ($timeOrScore == 0) {
            $this->players[$login] = array(-1 => 0);
        } else {
            $this->players[$login][$this->storage->currentMap->nbCheckpoints-1] = $timeOrScore;
        }

        $this->displayWidget();
    }

    public function onPlayerFinishLap($player, $time, $checkpoints, $nbLap)
    {
        $this->players[$player->login] = array(-1 => 0);
        $this->displayWidget();
    }

    public function onPlayerChangeSide($playerInfo, $old)
    {
        $player = Player::fromArray($playerInfo);
        $login = $player->login;

        if ($player->spectator) {
            if (isset($this->players[$login])) {
                unset($this->players[$login]);
            }
        } else {
            $this->players[$login] = array(-1 => 0);
        }

        $this->displayWidget();
    }

    public function onBeginMap($map, $warmUp, $matchContinuation)
    {
        $this->reset();
        $this->displayWidget();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        cpProgress::EraseAll();
    }

    public function eXpOnUnload()
    {
        CpProgress::EraseAll();
        $this->disableDedicatedEvents();
        $this->disableStorageEvents();
    }

    public function reset()
    {
        $this->players = array();
        foreach ($this->storage->players as $player) {
            $this->players[$player->login] = array(-1 => 0);
        }
    }
}