<?php

/*
* bpm_detect rev.3
* written by deseven
* dependencies: php 5.2+, soundtouch, (sox, mpg123 OR ffmpeg)
* website: http://deseven.info
*/

const USE_FFMPEG = true;
const USE_SOX = false;
const DETECT_LENGTH = false;
const SPLIT = true;
const DEF = false;

class bpm_detect {

	const split_seconds = 16;
	const default_path = "/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/root/bin";

	protected $workdir;
	protected $start_time;
	protected $split_seconds;
	protected $file;
	protected $file_ext;
	protected $file_length = 0;
	protected $delete_file = false;
	protected $use_ffmpeg = false;

	function __construct($filepath,$length = false,$use_ffmpeg = false,$path = false) {
		$this->start_time = microtime(true);
		if (!$path) {
			putenv("PATH=".$this::default_path);
		} else {
			putenv("PATH=".$path);
		}
		$this->workdir = getcwd();
		$filedir = pathinfo($filepath,PATHINFO_DIRNAME);
		if (strlen($filedir)) {
			$this->file = $filedir."/".pathinfo($filepath,PATHINFO_FILENAME);
		} else {
			$this->file = pathinfo($filepath,PATHINFO_FILENAME);
		}
		$this->file_ext = pathinfo($filepath,PATHINFO_EXTENSION);
		if ($use_ffmpeg) {
			$this->use_ffmpeg = true;
		}
		if (!$length) {
			if (!$this->use_ffmpeg) {
				exec('mpg123 -t "'.$this->file.".".$this->file_ext.'" 2>&1',$file_length);
				foreach ($file_length as $line) {
					if (strpos($line,$this->file.".".$this->file_ext." finished") !== false) {
						$line = preg_match("/\d+\:\d+/",$line,$match);
						$line = explode(":",$match[0]);
						$this->file_length = intval($line[0],10)*60 + intval($line[1],10);
						break;
					}
				}
			} else {
				exec('ffprobe "'.$this->file.".".$this->file_ext.'" -show_format 2>&1',$file_length);
				foreach ($file_length as $line) {
					if (strpos($line,"duration=") !== false) {
						$line = explode("=",$line);
						$this->file_length = round($line[1]);
						break;
					}
				}
			}
		} else {
			$this->file_length = $length;
		}
	}

	function __destruct() {
		if ($this->delete_file) {
			chdir($this->workdir);
			if (file_exists($this->file.".".$this->file_ext)) {
				unlink($this->file.".".$this->file_ext);
			}
		}
	}

	public function getProcessingTime() {
		$time = round(microtime(true) - $this->start_time,2);
		$this->start_time = microtime(true);
		return $time;
	}

	public function getSplitSeconds() {
		if ($this->split_seconds) {
			return $this->split_seconds;
		} else {
			return $this::split_seconds;
		}
	}

	private function convertToWAV() {
		if (!$this->use_ffmpeg) {
			exec('mpg123 -q -w "'.$this->file.'.wav" "'.$this->file.".".$this->file_ext.'" 2>&1');
		} else {
			exec('ffmpeg -y -i "'.$this->file.".".$this->file_ext.'" -acodec pcm_s16le "'.$this->file.'.wav" 2>&1');
		}
		$this->file_ext = "wav";
	}

	public function getLength() {
		return round($this->file_length,2);
	}

	public function detectBPM($split = false,$split_seconds = false) {
		$bpm = 0;
		if ($this->file_ext != "wav") {
			if ((!$this->use_ffmpeg) && ($this->file_ext != "mp3")) {
				return 0;
			} else {
				$this->convertToWAV();
				$this->delete_file = true;
			}
		}
		if (!$split_seconds) {
			$this->split_seconds = $this::split_seconds;
		} else {
			$this->split_seconds = $split_seconds;
		}
		if (file_exists($this->file.".".$this->file_ext)) {
			if ($split) {
				if (strlen($this->file_length)) {
					$pieces = ceil($this->file_length / $this->split_seconds);
					if ($pieces) {
						for ($piece = 1; $piece <= $pieces; $piece++) {
							if (!$this->use_ffmpeg) {
								exec('sox "'.$this->file.".".$this->file_ext.'" "'.$this->file."-$piece.".$this->file_ext.'" trim '.$piece * $this->split_seconds." ".$this->split_seconds." 2>&1");
							} else {
								exec('ffmpeg -y -ss '.$piece * $this->split_seconds." -t ".$this->split_seconds.' -i "'.$this->file.".".$this->file_ext.'" -acodec copy "'.$this->file."-$piece.".$this->file_ext.'" 2>&1');
							}
							if (file_exists($this->file."-$piece.".$this->file_ext)) {
								exec('soundstretch "'.$this->file."-$piece.".$this->file_ext.'" -bpm 2>&1',$piece_bpm);
								foreach ($piece_bpm as $line) {
									if (strpos($line,"Detected BPM rate") !== false) {
										$line = explode(" ",$line);
										$piece_bpm = round($line[3]);
										break;
									}
								}
								unlink($this->file."-$piece.".$this->file_ext);
								$pieces_bpm[] = round(intval($piece_bpm));
							}
						}
						if (count($pieces_bpm)) {
							$bpm = $pieces_bpm;
						}
					}
				}
			} else {
				$this->split_seconds = -1;
				exec('soundstretch "'.$this->file.".".$this->file_ext.'" -bpm 2>&1',$average_bpm);
				foreach ($average_bpm as $line) {
					if (strpos($line,"Detected BPM rate") !== false) {
						$line = explode(" ",$line);
						$average_bpm = round($line[3]);
						break;
					}
				}
				if ((!is_array($average_bpm)) && (strlen($average_bpm))) {
					$bpm = round($average_bpm);
				}
			}
		}
		return $bpm;
	}

}

?>