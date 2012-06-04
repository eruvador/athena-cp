<?php
if (!defined('ATHENA_ROOT')) exit;

function athena_get_default_bmp_data()
{
	$filename = sprintf('%s/emblem/%s', ATHENA_DATA_DIR, Athena::config('MissingEmblemBMP'));
	if (file_exists($filename)) {
		return file_get_contents($filename);
	}
}

function athena_display_empty_emblem()
{
	$data = athena_get_default_bmp_data();
	header("Content-Type: image/bmp");
	header('Content-Length: '.strlen($data));
	echo $data;
	exit;
}

if (Athena::config('ForceEmptyEmblem'))
	athena_display_empty_emblem();

$serverName       = $params->get('login');
$athenaServerName = $params->get('charmap');
$guildID          = intval($params->get('id'));
$athenaServer     = Athena::getAthenaServerByName($serverName, $athenaServerName);

if (!$athenaServer || $guildID < 0)
	athena_display_empty_emblem();
else {
	if ($interval=Athena::config('EmblemCacheInterval')) {
		$interval *= 60;
		$dirname   = ATHENA_DATA_DIR."/tmp/emblems/$serverName/$athenaServerName";
		$filename  = "$dirname/$guildID.png";
		
		if (!is_dir($dirname))
			if (Athena::config('RequireOwnership'))
				mkdir($dirname, 0700, true);
			else
				mkdir($dirname, 0777, true);
		elseif (file_exists($filename) && (time() - filemtime($filename)) < $interval) {
			header("Content-Type: image/png");
			header('Content-Length: '.filesize($filename));
			@readfile($filename);
			exit;
		}
	}
	
	$db  = $athenaServer->charMapDatabase;
	$sql = "SELECT emblem_len, emblem_data FROM $db.guild WHERE guild_id = ? LIMIT 1";
	$sth = $athenaServer->connection->getStatement($sql);
	$sth->execute(array($guildID));
	$res = $sth->fetch();
	
	if (!$res || !$res->emblem_len)
		athena_display_empty_emblem();
	else {
		require_once 'functions/imagecreatefrombmpstring.php';
		
		$data  = @gzuncompress(pack('H*', $res->emblem_data));
		$image = imagecreatefrombmpstring($data);
		
		header("Content-Type: image/png");
		
		if ($interval)
			imagepng($image, $filename);
		
		imagepng($image);
		exit;
	}
}
?>