<?php
/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Features\Tick\Event as TickEvent;

/**
 * This class allows you to create executions that will run in paralel with the rest of the application
 *
 * @package ManiaLivePlugins\eXpansion\Core
 */
class ParalelExecution implements \ManiaLive\Features\Tick\Listener
{

    private $pid;
    private $id;

    private $results = array();

    private $callback;

    private $lastCheck;

    private $return = 0;

    private $cmds = array();

    private $values;

    function __construct($cmds, $callback)
    {
	$this->id = time() . '.' . rand(0, 100000);
	$this->callback = $callback;

	if (!file_exists('tmp'))
	    mkdir('tmp/');

	$this->cmds = $cmds;

    }

    public function start(){
	$this->run();

	Dispatcher::register(TickEvent::getClass(), $this);
    }

    private function run()
    {
	$cmd = array_shift($this->cmds);

	$command = $cmd . ' >> tmp/' . $this->id . '.txt 2>&1 & echo';

	exec($command, $results, $this->return);
	$this->pid = $results[0];


	if ($this->pid == "")
	    $this->call();

	$this->lastCheck = time();
    }

    public function call()
    {
	$results = explode("\n", file_get_contents('tmp/' . $this->id . '.txt'));
	print_r($results);
	unlink('tmp/' . $this->id . '.txt');
	call_user_func($this->callback, $this, $results, $this->return);
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
	$this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
	return $this->id;
    }

    /**
     * @param int $pid
     */
    public function setPid($pid)
    {
	$this->pid = $pid;
    }

    /**
     * @return int
     */
    public function getPid()
    {
	return $this->pid;
    }

    /**
     * @param mixed $results
     */
    public function setResults($results)
    {
	$this->results = $results;
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
	return $this->results;
    }

    public function PsExists()
    {

	exec("ps ax | grep $this->pid 2>&1", $output);

	while (list(, $row) = each($output)) {

	    $row_array = explode(" ", $row);
	    $check_pid = $row_array[0];

	    if ($this->pid == $check_pid) {
		return true;
	    }

	}

	return false;
    }

    /**
     * Event launch every seconds
     */
    function onTick()
    {
	if (!$this->PsExists()) {
	    if (empty($this->cmds) || $this->return != 0) {
		$this->call();
		Dispatcher::unregister(TickEvent::getClass(), $this);
	    } else {
		$this->run();
	    }
	}
    }

    public function setValue($key, $val){
	$this->values[$key] = $val;
    }

    public function getValue($key){
	return $this->values[$key];
    }
}