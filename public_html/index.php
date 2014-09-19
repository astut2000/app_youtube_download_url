<?php require 'a/include.php' ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>YouTube Download URL</title>
	<link href="lib/bootstrap-3.1.1-dist/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link href="lib/jquery.jsonview/jquery.jsonview.css" type="text/css" rel="stylesheet">
</head>
<body>

<div class="container" style="margin-top: 50px; margin-bottom: 50px;">

    <h1>YouTube Download URL</h1>

	<br>

	<div class="row">
		<form class="form-horizontal" role="form">
			<div class="col-sm-10">
				<input name="url" type="text" class="form-control" placeholder="https://www.youtube.com/watch?v=d6kZ90_0eKc">
			</div>
			<div class="col-sm-2">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		</form>
	</div>

	<br />

	<?php if (!empty($_REQUEST['url'])): ?>

		<?php $video_info = get_video_info(get_video_id($_REQUEST['url'])) ?>
		<?php $fmt_list = explode(',', $video_info['fmt_list']) ?>
        <?php $dashmpd = new SimpleXMLElement(fetch($video_info['dashmpd'])) ?>

		<div class="row">
			<div class="col-sm-12">

				<h2><a href="https://www.youtube.com/watch?v=<?php e($video_info['video_id']) ?>"><?php e($video_info['title']) ?></a> by <a href="https://www.youtube.com/user/<?php e($video_info['author']) ?>"><?php e($video_info['author']) ?></a></h2>

				<iframe width="560" height="315" src="//www.youtube.com/embed/<?php e($video_info['video_id']) ?>" frameborder="0" allowfullscreen></iframe>

                <br />
                <br />

				<table class="table table-condensed table-bordered">
				<thead>
				<tr>
					<th>no</th>
					<th>size</th>
					<th>quality</th>
					<th>type</th>
					<th>url</th>
					<th>wget</th>
				</tr>
				</thead>
				<tbody>

				<?php foreach ($video_info['url_encoded_fmt_stream_map'] as $index => $stream): ?>
					<tr>
						<td><?php e($index + 1) ?></td>
						<td><?php $tmp = explode('/', $fmt_list[$index]); e($tmp[1]) ?></td>
						<td><?php e($stream['quality']) ?></td>
						<td><?php e($stream['type']) ?></td>
						<td><a href="<?php e($stream['url']) ?>">url</a></td>
						<td><input type="text" readonly value="wget -O <?php e(escapeshellarg($video_info['video_id'] . '.avi')) ?> <?php e(escapeshellarg($stream['url'])) ?>" style="width: 100%" onclick="$(this).select()"></td>
					</tr>
				<?php endforeach ?>

                <tr>
                    <th colspan="6">The following is audio-only streams</th>
                </tr>

                <?php foreach ($dashmpd->Period->AdaptationSet as $AdaptationSet): ?>
                    <?php foreach ($AdaptationSet->Representation as $Representation): ?>
                        <?php if (isset($Representation->AudioChannelConfiguration)): ?>
                            <tr>
                                <td><?php e(++$index + 1) ?></td>
                                <td><?php e($Representation->AudioChannelConfiguration['value']) ?> channels / <?php e($Representation['audioSamplingRate']) ?>Hz</td>
                                <td></td>
                                <td><?php e($AdaptationSet['mimeType']) ?>; codecs="<?php e($Representation['codecs']) ?>"</td>
                                <td><a href="<?php e($Representation->BaseURL) ?>">url</a></td>
                                <td><input type="text" readonly value="wget -O <?php e(escapeshellarg($video_info['video_id'] . '.avi')) ?> <?php e(escapeshellarg($Representation->BaseURL)) ?>" style="width: 100%" onclick="$(this).select()"></td>
                            </tr>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endforeach ?>

                <tr>
                    <th colspan="6">The following is video-only streams</th>
                </tr>

                <?php foreach ($dashmpd->Period->AdaptationSet as $AdaptationSet): ?>
                    <?php foreach ($AdaptationSet->Representation as $Representation): ?>
                        <?php if (!isset($Representation->AudioChannelConfiguration)): ?>
                            <tr>
                                <td><?php e(++$index + 1) ?></td>
                                <td><?php e($Representation['width']) ?>x<?php e($Representation['height']) ?></td>
                                <td></td>
                                <td><?php e($AdaptationSet['mimeType']) ?>; codecs="<?php e($Representation['codecs']) ?>"</td>
                                <td><a href="<?php e($Representation->BaseURL) ?>">url</a></td>
                                <td><input type="text" readonly value="wget -O <?php e(escapeshellarg($video_info['video_id'] . '.avi')) ?> <?php e(escapeshellarg($Representation->BaseURL)) ?>" style="width: 100%" onclick="$(this).select()"></td>
                            </tr>
                        <?php endif ?>
                    <?php endforeach ?>
                <?php endforeach ?>

				</tbody>
				</table>

                <h2>Technical Details</h2>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#tech-hide" role="tab" data-toggle="tab">Hide</a></li>
                    <li><a href="#tech-videoinfo" role="tab" data-toggle="tab">video_info</a></li>
                    <li><a href="#tech-dashmpd" role="tab" data-toggle="tab">video_info.dashmpd</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div id="tech-hide" style="font-size: 75%" class="tab-pane active"></div>
                    <div id="tech-videoinfo" style="font-size: 75%" class="tab-pane"></div>
                    <div id="tech-dashmpd" style="font-size: 75%" class="tab-pane"></div>
                </div>
            </div>
		</div>
	<?php endif ?>

	<h2>Credits</h2>
    <ul>
        <li><a href="http://stackoverflow.com/q/18876872">How to get the Lowest Quality YouTube video?</a></li>
        <li><a href="http://stackoverflow.com/q/480735">Select all contents of textbox when it receives focus (JavaScript or jQuery)</a></li>
    </ul>

</div>

<script src="lib/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="lib/bootstrap-3.1.1-dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="lib/knockout-3.1.0.js" type="text/javascript"></script>
<script src="lib/handlebars-v1.3.0.js" type="text/javascript"></script>

<?php if (!empty($_REQUEST['url'])): ?>

    <script src="lib/jquery.jsonview/jquery.jsonview.js" type="text/javascript"></script>
    <script type="text/javascript">
    jQuery(function ($) {
        $('#tech-videoinfo').JSONView(<?php echo json_encode($video_info) ?>);
        $('#tech-dashmpd').JSONView(<?php echo json_encode($dashmpd) ?>);
    });
    </script>

<?php endif ?>

</body>
</html>
