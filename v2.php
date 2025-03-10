<?php 

$id = $_GET['id']; 
$json = file_get_contents("http://212.224.88.65:8000/vidcdn/watch/$id");
$json = json_decode($json, true);
$sources_default = $json['sources_bk'];
foreach($sources_default as $sources_default){
    $m3u8 = $sources_default['file'];
}

?>

<head>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
</head>
<link rel="stylesheet" href="https://unpkg.com/plyr@3.7.2/dist/plyr.css">
<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js' type='text/javascript'> </script>
<script src="https://unpkg.com/plyr@3"></script>
<div id='video'
    class='player iframe video-js vjs-default-skin vjs-skin-flat-grey vjs-big-play-centered vjs-16-9 vidstreaming_iframe'>
    <script src='https://cdn.jsdelivr.net/hls.js/latest/hls.js'></script> 
    <video id='player' 
        controls crossorigin controls data-plyr-config></video>
    <script>
    var video = document.querySelector('#player');
    if (Hls.isSupported()) {
        var hls = new Hls();
        hls.loadSource('<?=$m3u8?>');
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });
    }
    hls.on(Hls.Events.ERROR, function(event, data) {
        console.log(data);
    });
    hls.on(Hls.Events.MANIFEST_PARSED, () => {
        player = loadPlayer();
    });

    function updateQuality(newQuality) {
        hls.levels.forEach((level, levelIndex) => {
            if (level.height === newQuality) {
                console.log("Found quality match with " + newQuality);
                hls.currentLevel = levelIndex;
            }
        });
    }

    function loadPlayer() {
        const defaultOptions = {}
        const availableQualities = hls.levels.map((l) => l.height)
        defaultOptions.quality = {
            default: availableQualities[1],
            
            options: availableQualities,
            forced: true,
            onChange: (e) => updateQuality(e),
        }
        

        const player = new Plyr('#player', defaultOptions);
        player.on('enterfullscreen', event => {
            screen.orientation.lock('landscape');
        });
        player.on('exitfullscreen', event => {
            screen.orientation.lock('portrait');
        });
        player.on('play', event => {
            console.log(player.duration)
        });
    }
    </script>
</div>