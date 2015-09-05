<?php

class RSNImportSources_Utils_Performance {
	var $startTime;
	var $maxItems = 0;
	var $prevPercent = 0;
	var $tickCounter = 0;
	
	public function __construct($maxItems = 0){
		$this->maxItems = $maxItems;
		$this->startTime = new DateTime();
	}
	
	public function tick(){
		$this->tickCounter++;
		$perfPC = (int)($this->tickCounter/$this->maxItems * 100);
		if($this->prevPercent != $perfPC){
			$perfNow = new DateTime();
			$perfElapsed = date_diff($this->startTime, $perfNow)->format('%H:%i:%S');
			echo "\n import $this->tickCounter/$this->maxItems "
				."( $perfPC %, $perfElapsed, "
				. self::getMemoryUsage()
				." ) ";
			$this->prevPercent = $perfPC;
		}
	}
	public function terminate(){
		$perfPC = (int)($this->tickCounter/$this->maxItems * 100);
		$perfNow = new DateTime();
		$perfElapsed = date_diff($this->startTime, $perfNow)->format('%H:%i:%s');
		echo "\n Importation terminée pour $this->tickCounter/$this->maxItems "
			."( $perfPC %, $perfElapsed, "
			.", memoire : ". self::getMemoryUsage()
			." ) ";
	}
	static function getMemoryUsage(){
		$size = memory_get_usage();
		$unit=array('B','KB','MB','GB','TB','PB');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
}
