<?php

use Streaming\FFMpeg;

require 'vendor/autoload.php'; 

define('APP_PATH', dirname(__FILE__));

$ffmpeg = ffmpeg();

$video = $ffmpeg->open(APP_PATH.'/video.mp4');

$video->hls()
    ->x264()
    ->autoGenerateRepresentations([720, 360]) // You can limit the number of representatons
    ->save(APP_PATH.'/stream/video.m3u8');