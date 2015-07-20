<?php namespace App\Tophub;

class Logger {
	
	/* 
		Logger::writeln('error', 'hello error'); 		// white on red
		Logger::writeln('info', 'hello info'); 			// green 
		Logger::writeln('comment', 'hello comment'); 	// copper
		Logger::writeln('question', 'hello question'); 	// black on cyan
	*/
	static function writeln($lvl='info', $str = '') {
		$output = new \Symfony\Component\Console\Output\ConsoleOutput();
		$output->writeln("<$lvl>$str</$lvl>"); 
	}
}
