<?php

function fetch($url)
{
	$d = __DIR__;

	$md5 = md5($url);
	$cache_file = "$d/../../cache/$md5";
	if (!file_exists($cache_file) || filemtime($cache_file) < strtotime('-1 hour')) {
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

	// id: nCnJ_x-QpJM
	//
	// array (size=8)
	//   'errorcode' => string '150' (length=3)
	//   'reason' => string 'This video contains content from Quiz Group Pro. It is restricted from playback on certain sites.<br/><u><a href='http://www.youtube.com/watch?v=nCnJ_x-QpJM&feature=player_embedded' target='_blank'>Watch on YouTube</a></u>' (length=222)
	//   'status' => string 'fail' (length=4)
	//   'eventid' => string 'Aq4qVMLdN4ab-gOroIHwDQ' (length=22)
	//   'errordetail' => string '0' (length=1)
	//   'csi_page_type' => string 'embed' (length=5)
	//   'c' => string 'WEB' (length=3)
	//   'enablecsi' => string '1' (length=1)
	parse_str($html, $video_info);
	if (!empty($video_info['errorcode'])) {
		throw new Exception(__FUNCTION__ . ': ' . json_encode($video_info));
	}

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
