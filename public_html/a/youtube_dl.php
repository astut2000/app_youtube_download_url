<?php

function fetch($url)
{
	$d = __DIR__;

	$md5 = md5($url);
	$cache_file = "$d/../../cache/$md5";
	if (!file_exists($cache_file) || filemtime($file) < strtotime('-1 hour')) {
		$html = file_get_contents($url);
		file_put_contents($cache_file, $html);
	}
	else {
		$html = file_get_contents($cache_file);
	}

	return $html;
}

function get_video_id($url)
{
	$query = parse_url($url, PHP_URL_QUERY);
	parse_str($query, $a);
	return $a['v'];
}

function get_video_info($id)
{
	$html = fetch("http://www.youtube.com/get_video_info?video_id=$id");

	parse_str($html, $video_info);

	$tmp = array();
	foreach(explode(',', $video_info['url_encoded_fmt_stream_map']) as $stream_str) {
		parse_str($stream_str, $stream);
		$tmp[] = $stream;
	}
	$video_info['url_encoded_fmt_stream_map'] = $tmp;

	$tmp = array();
	foreach (explode(',', $video_info['adaptive_fmts']) as $fmt_str) {
		parse_str($fmt_str, $fmt);
		$tmp[] = $fmt;
	}
	$video_info['adaptive_fmts'] = $tmp;

	return $video_info;
}
