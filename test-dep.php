<?php

$isCLI = (php_sapi_name() == 'cli');

function getVersion($cmd,$search) {
	exec("command -v $cmd 2>&1",$out,$code);
	if ($code == 0) {
		exec("$cmd --version 2>&1",$version);
		foreach ($version as $line) {
			if (strpos($line,$search) !== false) {
				preg_match("/\d+\.\d+\.\d+/",$line,$match);
				return $match[0];
			}
		}
	}
	return false;
}

$soundstretch_version = getVersion("soundstretch","SoundStretch");
$sox_version = getVersion("sox","SoX");
$mpg123_version = getVersion("mpg123","mpg123");
$ffmpeg_version = getVersion("ffmpeg","version");

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