<?php

$isCLI = (php_sapi_name() == 'cli');

function getSoxVersion() {
	exec("sox --version 2>&1",$sox_version);
	foreach ($sox_version as $line) {
		if (strpos($line,"SoX") !== false) {
			preg_match("/\d+\.\d+\.\d+/",$line,$match);
			return $match[0];
		}
	}
	return false;
}

function getMpg123Version() {
	exec("mpg123 --version 2>&1",$mpg123_version);
	foreach ($mpg123_version as $line) {
		if (strpos($line,"mpg123") !== false) {
			preg_match("/\d+\.\d+\.\d+/",$line,$match);
			return $match[0];
		}
	}
	return false;
}

function getSoundStretchVersion() {
	exec("soundstretch 2>&1",$soundstretch_version);
	foreach ($soundstretch_version as $line) {
		if (strpos($line,"SoundStretch") !== false) {
			preg_match("/\d+\.\d+\.\d+/",$line,$match);
			return $match[0];
		}
	}
	return false;	
}

function getFFmpegVersion() {
	exec("ffmpeg -version 2>&1",$ffmpeg_version);
	foreach ($ffmpeg_version as $line) {
		if (strpos($line,"version") !== false) {
			preg_match("/\d+\.\d+\.\d+/",$line,$match);
			return $match[0];
		}
	}
	return false;	
}

$soundstretch_version = getSoundStretchVersion();
$sox_version = getSoxVersion();
$mpg123_version = getMpg123Version();
$ffmpeg_version = getFFmpegVersion();

echo "SoundTouch: \t";
if ($soundstretch_version !== false) {
	echo "[PASS] detected SoundStretch ".$soundstretch_version;
} else {
	echo "[FAIL] can't find a suitable SoundStretch";
}

echo ($isCLI ? "\n" : "<br>");

echo "SoX:        \t";
if ($sox_version !== false) {
	echo "[PASS] detected SoX ".$sox_version;
} else {
	echo "[FAIL] can't find a suitable SoX";
}

echo ($isCLI ? "\n" : "<br>");

echo "mpg123:     \t";
if ($mpg123_version !== false) {
	echo "[PASS] detected mpg123 ".$mpg123_version;
} else {
	echo "[FAIL] can't find a suitable mpg123";
}

echo ($isCLI ? "\n" : "<br>");

echo "ffmpeg:     \t";
if ($ffmpeg_version !== false) {
	echo "[PASS] detected ffmpeg ".$ffmpeg_version;
} else {
	echo "[FAIL] can't find a suitable ffmpeg";
}

echo ($isCLI ? "\n" : "<br>");

?>