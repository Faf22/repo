<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
<title>RatioFaker Online - iHack.fr</title>
<link href="styles.css" rel="stylesheet" type="text/css" />
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
 <script src="js/countdown.js"></script>
 <link rel="shortcut icon" href="favicon.ico">
</head>
<body>
<div style="display:none">
  <?php
require_once ("php/faker.php"); 
require_once ("php/BDecode.php");
require_once ("php/BEncode.php");

//*********************************************//functions
		 function calcfilesize($bytes)
        {
        if ($bytes < 1000 * 1024)
            return number_format($bytes / 1024, 2) . "ko";
        elseif ($bytes < 1000 * 1048576)
            return number_format($bytes / 1048576, 2) . "Mo";
        elseif ($bytes < 1000 * 1073741824)
            return number_format($bytes / 1073741824, 2) . "Go";
        else
            return number_format($bytes / 1099511627776, 2) . "To";
        }
		

		
		 function checktorrent($alltorrent)
		{
			$debutTorrent = "";
			for ($i=0;$i<11;$i++)
			{
				$debutTorrent .= $alltorrent[$i];
			}
			if($debutTorrent == "d8:announce")
			{
				//echo $debutTorrent;
				//echo " Le fichier est bien un torrent <br />";
			}
			else
			{
				//echo $debutTorrent;
				exit (" Erreur le fichier n'est pas un torrent");
			}
		}		
		 function calchash($alltorrent)
		{
			$array = BDecode($alltorrent);
			if (!$array)
			{
				echo "<p class=\"error\">There was an error handling your uploaded torrent. The parser didn't like it.</p>";
				endOutput();
				exit;
			}
			$hash = sha1(BEncode($array["info"]), TRUE);
			$hash = urlencode($hash);
			$hash = str_replace(CHR(32),"",$hash);
			echo $hash;
			
		}

		function trackerurl($filesTorrentSize,$alltorrent)
		{
			$compt=0;
			$tailleFichier = '';
			$urlfound = false;
			$namefound = false;
			$datefound = false;
			for ($i=0;$i<$filesTorrentSize;$i++)
			{
				
				//************//trouver l'url  du tracker
				if ($alltorrent[$i] == "e" and $urlfound != true)
				{
					$b = $i;
					$index = $alltorrent[$b+1] . $alltorrent[$b+2];
					$index = $b+4+$index;
					$urlTracker = "";
					for ($j=$b+4;$j<$index;$j++)
					{
						$urlTracker .= $alltorrent[$j];
						
					}
					echo "$urlTracker";
					$urlfound = true;
					
					
					}
				
				}
			}
			
		
		 function parcourirtorrent($filesTorrentSize,$alltorrent)
		{
			$compt=0;
			$tailleFichier = '';
			$urlfound = false;
			$namefound = false;
			$datefound = false;
			for ($i=0;$i<$filesTorrentSize;$i++)
			{
				//************//nom du torrent
				if ($alltorrent[$i] == "n" and $namefound !=true)
				{
					$a = $i;
					$name = $alltorrent[$a].$alltorrent[$a+1].$alltorrent[$a+2].$alltorrent[$a+3];
					if ($name == "name")
					{
						$index = $alltorrent[$a+4].$alltorrent[$a+5];
						
							$index = $a+7+$index;
							$nameTorrent = "";
							for ($j=$a+7;$j<$index;$j++)
							{
								$nameTorrent .= $alltorrent[$j];
								
							}
							echo "<br /> <b>Nom du torrent :</b> <br> $nameTorrent <br />";
							$namefound = true;
					}
					
				}
				//************//trouver l'url  du tracker
				if ($alltorrent[$i] == "e" and $urlfound != true)
				{
					$b = $i;
					$index = $alltorrent[$b+1] . $alltorrent[$b+2];
					$index = $b+4+$index;
					$urlTracker = "";
					for ($j=$b+4;$j<$index;$j++)
					{
						$urlTracker .= $alltorrent[$j];
						
					}
					echo " <br /><b>URL du tracker:</b> <br>$urlTracker <br />";
					$urlfound = true;
					$tracker = $urlTracker;
					
				}
				//************//trouver la date création du torrent 
				if ($alltorrent[$i] == "d" and $datefound != true)
				{
					$d = $i;
					$datei = $alltorrent[$d] . $alltorrent[$d+1] . $alltorrent[$d+2] . $alltorrent[$d+3].  $alltorrent[$d+4];
					if ($datei == "datei")
					{
	 					$indexdepart = $d+4;
						$timestamp = "";
						for ($j=$indexdepart+1;$j<$indexdepart+11;$j++)
						{
							$timestamp .= $alltorrent[$j];	
						}
						$date = date('d/m/Y à H:i',$timestamp);
						//echo "<b>Date de création du .torrent:</b> $date<br/>";
						$datefound = true;
					}
					
				}
				//************//trouver la taille total du fichier
				if ($alltorrent[$i] == 'l')
				{
					$c = $i;
					$checklength = $alltorrent[$c].$alltorrent[$c+1].$alltorrent[$c+2].$alltorrent[$c+3].$alltorrent[$c+4].$alltorrent[$c+5];
					//echo "<b>doit contenir lenght:</b> $checklength";
					if ($checklength == "length")
					{
						
						for ($d=$c+7;$d<$filesTorrentSize;$d++)
						{	
						
						
							//echo " <b>variable parcoure : </b>$alltorrent[$d]";
							if ($alltorrent[$d] == 'e')
							{
								$compt++;
								$tabTailleFichier[$compt]=$tailleFichier;
								$tailleFichier = '';
								break;
							}
							else
							{
								$tailleFichier .= $alltorrent[$d];
								//echo " <b>!!!!taille fichier!!!!!:</b> $tailleFichier";
								
							}
						}					
					}
				}
			}
			$globalPoid=0;
			for ($b=1;$b<=$compt;$b++)
			{
				//echo " >>Fichiers $b et sa tailles : $tabTailleFichier[$b]";
				$globalPoid =  $globalPoid+$tabTailleFichier[$b];
			}
			$calcfilesize = calcfilesize($globalPoid);
			echo " <b>Poids total du dossier :</b> $calcfilesize <br />";
			// echo " <b>Nombre de fichiers :</b> $compt <br />";
		}
		

		
		
//*********************************************//main()	
	
if (isset($_FILES["torrent"]))
{
		//is_uploaded_file($_FILES["torrent"]["tmp_name"]) or die("<p class=\"error\">File upload error 2</p>\n");
		$filesTorrent = fopen($_FILES["torrent"]["tmp_name"], "rb") or die(" File check error");
		$filesTorrentSize = filesize($_FILES["torrent"]["tmp_name"]);
		$alltorrent = fread($filesTorrent, $filesTorrentSize);
		
		//************//Verif si le fichier est un torrent
		checktorrent($alltorrent);
		//************//calcul du hash 
		calchash($alltorrent);
		//************//Debut
		parcourirtorrent($filesTorrentSize,$alltorrent);
		//************//End
}

?>

</div>

<div id="faker">
 <div id="title"> <h2>ratio faker ihack.fr - 3/3</h2></div>
  <p><?php  echo "Simuler un envoi de <b>$ul2</b> à <b>$speed Mo/s</b>" ?>
    <br />
    <br />
<i>**User Agent conseillé: uTorrent/3320
  </i></p>

   <form onsubmit="return
false;">
	  <fieldset id="countdown-start" style="border:none">
	
	    <input type="hidden" id="hours" type="text" min="0" max="23" step="1" value="<?php echo $heures ?>" size="3" readonly="readonly" />
		  
	    <input type="hidden" id="minutes" type="text" min="0" max="59" step="1" value="<?php echo $minutes ?>" size="3" readonly="readonly" />
		
		  <input type="hidden" id="seconds" type="text" min="0" max="59" step="1" value="<?php echo $secondes ?>" size="3" readonly="readonly" />
	
	  <a href="<?php echo $start ?>" target="_blank"><input type="button" class="button" id="start" onClick="startCountdown()" onmouseout="this.disabled=true" value="Demarrer"/></a>
	  </fieldset>
	</form>
<p class="timer" id="clock-placeholder"></p>
<script>
		var clockRunning = false;
	  var clockPlaceholder = document.getElementById("clock-placeholder");
	  var targetDate;
	  var clock;
	  function updateClock() {
	    var cd = countdown(targetDate, null, countdown.DAYS|countdown.HOURS|countdown.MINUTES|countdown.SECONDS, 4);
	    clockPlaceholder.innerHTML = cd.toString();
	    if (targetDate.getTime() < (new Date()).getTime()) {
	    <!-- window.open('<?php echo $stop ?>','_blank'); -->
		  endCountdown(); 
	    }
	  }
	  function startCountdown() {
	    if ( clockRunning == false ) {
	      var h = parseInt(document.getElementById("hours").value) || 0;
	      var m = parseInt(document.getElementById("minutes").value) || 0;
	      var s = parseInt(document.getElementById("seconds").value) || 0;
	      if (h == 0 && m == 0 && s == 0 ) {
	        return;
	      }
	      // setup target Date object
	      var now = new Date(); // console.debug(now.toString());
	      targetDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(),
	                            now.getHours() + h, now.getMinutes() + m, now.getSeconds() + s, 0);
	      // start clock
	      clock = setInterval(updateClock, 1000);
	      clockRunning = true;
	    }
	  }
	  function endCountdown() {
	    clockPlaceholder.innerHTML = '<a href="<?php echo $stop ?>" target="_blank"><input type="button" class="buttonbleu" value="Terminer" onmouseout="this.disabled=true" /></a>'
	    clearInterval( clock );
	    clockRunning = false;
	  }
	</script>
    


</div>

</body>
</html>