<?php
// browserarchive.org aka browsers.evolt.org
// Copyright (c) 1999-2017 Adrian Roselli, William Anderson

$headertext = "browsers.evolt.org: ";
$path=urldecode($_GET["file"]); //remove url encoding.
$path=str_replace("..","",$path); // disallow directory up.
$path=preg_replace("/\/+/", "/", $path); // remove duplicate slashes.
if($path == "/") { $path = ""; }
if($path[0]=="/"){ $path=substr($path,1,strlen($path)-1); } // Remove leading slash.
if(substr($path,strlen($path)-1,1) =="/"){ $path=substr($path,0,strlen($path)-1);} // Remove trailling slash.
$filerequest = $path;
if ( $filerequest == "" || $filerequest == "archive" ) { header("Location: /",302); }

include "assets/inc/00-functions.inc";
include "data.inc";
$browserreq = array_slice(explode("/","archive/".$filerequest),1,1);
$browsercode = $browserreq[0];
$site_name = "browsers.evolt.org";
$html_title = $browsers[$browsercode]["name"];
if ( $browsercode != "" ) {
	$page_title = $browsers[$browsercode]["name"];
} else {
	$page_title = $site_name;
}
$short_title = $browsers[$browsercode]["name"];
$page_type = "page";
$upstairs_uri = "/";
$upstairs_title = "Browser Archive";
include "assets/inc/10-header.inc";
include "assets/inc/20-message.inc";
?>
<div class="wrapper">
    <section id="content" class="full-width" role="main">
    	<div class="p100">
			<p class="notice error"><strong>Pardon our dust!</strong> This iteration of <strong>browsers.evolt.org</strong> is under test, containing content which has not yet been finalized.</p>
		</div>
		<div class="p66">
			<!-- h2><?php echo "/archive/".$filerequest ?></h2 -->
<?php
	// start the skinning process mister spock
	// include("beodl/beo-header.html");

	// this is probably an evil way to replicate the unix
	// basename function, which grabs the actual filename
	// from a full directory path, e.g. /usr/bin/ls would
	// return ls.
	for ($token = strtok($filerequest, "/");
		$token != "";
		$token = strtok("/"))
	{
		$dirstructure[] = $token;
	}
	$fileBasename = count($dirstructure) - 1;
	$filecheck = "/archive/" . $filerequest;

	// hey, say thanks and kick off an unordered list would ya?
	$downloadText1 = <<< TEXTEND
TEXTEND;

	$rawFilesize = filesize(substr($filecheck,1,strlen($filecheck)));
	$filesizeBits = $rawFilesize * 8;
	$filesizeBytes = number_format($rawFilesize);
	$filesizeKBytes = number_format($rawFilesize / 1024, 2);
	$filesizeMBytes = number_format($rawFilesize / 1024 / 1024, 2);

	// the +1 is just a crude uprounding method - if anyone
	// knows of a better way, do let me know ;)
	$time336 = ( $filesizeBits / 34406 ) + 1;
	$time56K = ( $filesizeBits / 57334 ) + 1;
	$timeISDN = ( $filesizeBits / 131072 ) + 1;
	$timeADSL = ( $filesizeBits / 524288 ) + 1;
	$timeT1 = ( $filesizeBits / 1572864 ) + 1;

	$downloadTime336 = number_format($time336, 0) . " secs";
	if ( $time336 > 60 ) { $downloadTime336 = number_format($time336 / 60, 0) . " mins"; }
	if ( $time336 > 3600 ) { $downloadTime336 = number_format($time336 / 60 / 60, 2) . " hrs"; }
	$downloadTime56K = number_format($time56K, 0) . " secs";
	if ( $time56K > 60 ) { $downloadTime56K = number_format($time56K / 60, 0) . " mins"; }
	if ( $time56K > 3600 ) { $downloadTime56K = number_format($time56K / 60 / 60, 2) . " hrs"; }
	$downloadTimeISDN = number_format($timeISDN, 0) . " secs";
	if ( $timeISDN > 60 ) { $downloadTimeISDN = number_format($timeISDN / 60, 0) . " mins"; }
	if ( $timeISDN > 3600 ) { $downloadTimeISDN = number_format($timeISDN / 60 / 60, 2) . " hrs"; }
	$downloadTimeADSL = number_format($timeADSL, 0) . " secs";
	if ( $timeADSL > 60 ) { $downloadTimeADSL = number_format($timeADSL / 60, 0) . " mins"; }
	if ( $timeADSL > 3600 ) { $downloadTimeADSL = number_format($timeADSL / 60 / 60, 2) . " hrs"; }
	$downloadTimeT1 = number_format($timeT1, 0) . " secs";
	if ( $timeT1 > 60 ) { $downloadTimeT1 = number_format($timeT1 / 60, 0) . " mins"; }
	if ( $timeT1 > 3600 ) { $downloadTimeT1 = number_format($timeT1 / 60, 2) . " hrs"; }
?>
<p><strong class="title">
Thanks for using evolt.org's Browser Archive &#8212; the Internet's leading
source for web browsers, old and new.
</strong></p>

<p>
The file <strong><?php echo basename($filerequest); ?></strong> (<?php echo $filesizeKBytes; ?> KB / <?php echo $filesizeMBytes; ?> MB) can be downloaded by clicking on one of the mirror links below.
</p>

<?php
	// hmmm, let's find out what mirrors we can use
	$dataFile = "beodl/mirrors.csv";
	// and let's play nice with the file system ...
	if (file_exists($dataFile))
	{
		$browserFileOK = "yes";
	}
	else
	{
		$browserFileOK = "no";
		$downloadError = "couldn't open data file for browsers";
	}

	// ok, if our mirror data file exists ...
	if ($browserFileOK == "yes")
	{
		// ... then let's take a peek ...
		if ($downloadFile = fopen($dataFile,"r"))
		{
			?>
<hr /><table border="0" cellpadding="5" cellspacing="0">
			<?php
			// ... and let's keep goin' until it's empty
			while(!feof($downloadFile))
			{
				// "Show me, what you're made of"
				$downloadField = fgetcsv($downloadFile,1024);

				// "I've seen things you people wouldn't believe"
				$downloadPath = $downloadField[2]."/".$filerequest;

				// "Attack ships on fire, off the shoulder of Orion"
				// added the 'mirror disabled?' check -- neuro 2004-05-18
				if ( $downloadField[1] != "" && !file_exists("mirrors/sites/".$downloadField[0]."/disable") )
				{
					// print "<img src=\"/assets/flags/$downloadField[3].gif\" alt=\"[$downloadField[3]/$downloadField[6]]\" /> <a href=\"$downloadPath\">$downloadField[1]</a><br />\n";
?><tr><td><img src="/assets/external/icondrawer/flags/flags_iso/16/<?php echo $downloadField[3]; ?>.png" style="vertical-align:middle" height="16" width="16" alt="[<?php echo $downloadField[3]."/".$downloadField[6]; ?>]" />&nbsp;</td><td>Download <a href="<?php echo $downloadPath; ?>"><?php echo basename($downloadPath); ?></a> from <?php echo $downloadField[1]; ?></td></tr><?php
				}
			}
			// we don't need this stinking file anymore!!
			fclose($downloadFile);
			?>
</table><br /><hr />
			<?php
		}
		else
		// bad things ...
		{
			print "&raquo; $downloadError<br>\n";
		}
	}

	// clear out of the unordered list
	//show the above text and get the hell outta here

    if ( time () < filectime(substr($filecheck,1,strlen($filecheck))) + 172800 ) {
            ?> <em>Please note that this file was added or modified less than 48 hours ago, and may not be available from our mirrors yet.</em><hr /><?php
    }

?>
<p>
These downloads are made available by our mirror providers who help us
deliver evolt.org services worldwide. Please support the individuals and
organisations who support evolt.org.
</p> <!--
<p>
If you would like to support evolt.org and have the resources to share
the Browser Archive load, you can read more about becoming a mirroring
sponsor: <a href="http://evolt.org/somewhere/">Mirror the Browser Archive</a>.
</p> -->
<p>
Testing with browsers from the archive keeps your clients happy and your
services professional. Read about <a href="http://evolt.org/help_support_evolt/">how you can support evolt.org</a>.
</p>
<p>
In addition to the Browser Archive, evolt.org also provides a number of
resources for the Web development community including our member-driven
email discussion list
<a href="http://lists.evolt.org/mailman/listinfo/thelist">thelist</a>
and our Web site <a href="http://evolt.org/">evolt.org</a> containing
current articles, tutorials and news.
</p>
<p>
If you have any problems with the Browser Archive or any questions about
evolt.org, please <a href="http://evolt.org/contact/">let us know</a>.
</p>
<?php

	// awww, beo has feet?  how cute!
	// include("beodl/beo-footer.adsense.html");
	// include("beodl/beo-footer.html");
?>

		</div>
		<?php include "assets/inc/30-adsense.inc" ?>
	</section>
</div>
<?php
include "assets/inc/99-footer.inc";
?>
