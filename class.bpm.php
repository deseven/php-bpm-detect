<?php

/*
* bpm_detect rev.1
* written by deseven
* dependencies: php 5.2+, soundtouch, (sox, mpg123 OR ffmpeg)
* website: http://deseven.info
*/

class bpm_detect {

	const split_seconds = 16;
	const default_path = "/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin:/root/bin";

	protected $workdir;
	protected $start_time;
	protected $split_seconds;
	protected $file;
	protected $file_ext;
	protected $file_length;
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
		$this->file = pathinfo($filepath,PATHINFO_FILENAME);
		$this->file_ext = pathinfo($filepath,PATHINFO_EXTENSION);
		if ($use_ffmpeg) {
			$this->use_ffmpeg = true;
		}
		if (!$length) {
			if (!$this->use_ffmpeg) {
				$file_length = exec('mpg123 -t '.$this->file.".".$this->file_ext.' 2>&1 | grep "finished" | cut -f1 -d"]" | tr -d "["');
				$file_length = explode(":",$file_length);
				$this->file_length = intval($file_length[0],10)*60 + intval($file_length[1],10);
			} else {
				$file_length = exec('ffprobe '.$this->file.".".$this->file_ext.' -show_format 2>&1 | grep "duration=" | cut -f2 -d"="');
				$this->file_length = round($file_length);
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
			exec("mpg123 -q -w ".$this->file.".wav ".$this->file.".".$this->file_ext." 2>&1");
		} else {
			exec("ffmpeg -i ".$this->file.".".$this->file_ext." -acodec pcm_s16le ".$this->file.".wav 2>&1");
		}
		$this->file_ext = "wav";
	}

	public function getLength() {
		return round($this->file_length,2);
	}

	public function detectBPM($split = false,$split_seconds = false) {
		$bpm = 0;
		if ($this->file_ext == "mp3") {
			$this->convertToWAV();
			$this->delete_file = true;
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
								exec("sox ".$this->file.".".$this->file_ext." ".$this->file."-$piece.".$this->file_ext." trim ".$piece * $this->split_seconds." ".$this->split_seconds." 2>&1");
							} else {
								exec("ffmpeg -ss ".$piece * $this->split_seconds." -t ".$this->split_seconds." -i ".$this->file.".".$this->file_ext." -acodec copy ".$this->file."-$piece.".$this->file_ext." 2>&1");
							}
							if (file_exists($this->file."-$piece.".$this->file_ext)) {
								$piece_bpm = exec("soundstretch ".$this->file."-$piece.".$this->file_ext.' -bpm 2>&1 | grep "Detected" | cut -f4 -d" "');
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
				$average_bpm = exec("soundstretch ".$this->file.".".$this->file_ext.' -bpm 2>&1 | grep "Detected" | cut -f4 -d" "');
				if (strlen($average_bpm)) {
					$bpm = round($average_bpm);
				}
			}
		}
		return $bpm;
	}

}

?>