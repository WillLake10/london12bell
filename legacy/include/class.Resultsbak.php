<?php

class Results
{
	private $iYear;
	private $xResult;

	function Results()
	{
		$this->iYear = null;
		$this->sResults = null;
	}
	
	function fetch_year($iYear)
	{
                $this->iYear = $iYear;
		$results = new SimpleXMLElement(file_get_contents('./results/' . $this->iYear . '.xml'));
		foreach($results->result as $result)
		{
			if($result->year == $iYear)
			{
				$this->xResult = $result;
			}
		}
	}

	function show_table()
	{
		global $sTopPath;
		
		if(is_null($this->xResult)) {
			print "<p>Results are not available for this year.</p>";
		}
		else {
			$bDetails = ($this->xResult->xpath('band/photo') || $this->xResult->xpath('band/ringers'));
                        $bTouches = $this->xResult->xpath('band/touch');
			$bSpeed = $this->xResult->xpath('band/speed');
			$aResult = $this->xResult->xpath('band');
			usort($aResult, 'sort_results');
			print "<table class=\"resultstable\">";
			print "<tr><th>Position</th><th>Rang</th><th>Team</th><th>Mark</th>".($bSpeed?"<th>Speed</th>":"").($bDetails?"<th></th>":"").($bTouches?"<th></th>":"")."</tr>";
                        $lastPos = NULL;
                        $lastMark = NULL;
			foreach($aResult as $iPos=>$xBand)
			{
                            if (is_null($lastMark) || (string)$lastMark != (string)$xBand->mark) {
                                $pos = $iPos + 1;
                            } else {
                                $pos = $lastPos . "=";
                            }
                            $lastPos = $pos;
                            $lastMark = $xBand->mark;
				$sName = $xBand->name;				
				print "<tr><td>".$pos."</td><td>".make_ordinal($xBand->rang)."</td><td>".$sName."</td><td>".$xBand->mark."%</td>";

    			 	if($bSpeed) {
           				print "<td>".$xBand->speed."</td>";
      			 	}

				if($xBand->photo || $xBand->ringers) {
					print '<td class="button"><a href="'.$sTopPath.'banddetails.php?band='.$xBand->rang.'&year='.$this->xResult->year.'" year="'.$this->xResult->year.'" rang="'.$xBand->rang.'" onclick=\'ga("send", "event", "detail click", "'.$this->xResult->year.'", "'.str_replace("'", "&apos;", $sName).'");\'><img src="images/detailbutton.png" alt="view details" title="view details" /></a></td>';
				}
                                if($xBand->touch) {
                                    print '<td><audio year="'.$this->xResult->year.'" band="'.str_replace("'", "&apos;", $sName).'" controls preload="metadata"><source src="'.$sTopPath.'touches/'.$this->xResult->year.'/'.$xBand->touch.'" type="audio/mpeg" /><a href="'.$sTopPath.'touches/'.$this->xResult->year.'/'.$xBand->touch.'" onclick=\'ga("send", "event", "touch click", "'.$this->xResult->year.'", "'.str_replace("'", "&apos;", $sName).'");\'>touch</a></td>';
                                }
				print "</tr>";
			}
			print "</table>\n";
			print '<script type="text/javascript">';
			print "var showResults = function(data){
						if($('.bandoverlay').length == 0){
							var wDiv = document.createElement('div');
							var rDiv = document.createElement('div');
							$(wDiv).addClass('bandoverlay');
							$('.resultstable').after(wDiv);
							$(wDiv).append($(rDiv));
							$(rDiv).append('<a class=\"closeoverlay\">Close</a>');
							$(rDiv).append(data);
							
							$('.closeoverlay').live('click',function(){\$('.bandoverlay').remove();});
						}
													};\n";
			print "$('.button').find('a').live('click',function(){\$.get('{$sTopPath}ajaxdetails.php?band='+$(this).attr('rang')+'&year='+$(this).attr('year'),function(data){showResults(data)}); return false;});";
			print '</script>';
		}
	}
	
	function get_band_photo($iBand) {
		$aBand = $this->xResult->xpath("band[rang=$iBand]");
		if(count($aBand) != 1) {
			return false;
		}
		$aBand = $aBand[0];
		return $aBand->photo;
	}
	
	function get_band_name($iBand) {
		$aBand = $this->xResult->xpath("band[rang=$iBand]");
		if(count($aBand) != 1) {
			return false;
		}
		$aBand = $aBand[0];
		return $aBand->name;
	}
	
	function get_band_caption($iBand) {
		$aBand = $this->xResult->xpath("band[rang=$iBand]");
		if(count($aBand) != 1) {
			return false;
		}
		$aBand = $aBand[0];
		if(!$aBand->ringers) {
			return false;
		}
		else {
			$aCaption = array();
			$aMissing = array();
			$sConductor = $aBand->conductor;
			for($i = 1; $i < 13; $i++) {
				$sRinger = $aBand->ringers->xPath('r'.$i);
				$sRinger = $sRinger[0];
				$aAttributes = $sRinger->attributes();
				$sRinger .= "";
				if($sConductor == $sRinger || $sConductor == $i) {
					$sRinger .= " (C)";
				}
				if($sRinger) {
					if($aAttributes["missing"]) {
						$aMissing[] = str_replace(' ','&nbsp;',"$i $sRinger");
					}
					else {
						$aCaption[] = str_replace(' ','&nbsp;',"$i $sRinger");
					}
				} else {
					var_dump($sRinger);
					var_dump($aBand->ringers->xPath('r'.$i));
				}
			}
						
		}
		$sIntro = $aBand->photocaption;
		$sCaption = "<p class='bandcaption'>".($sIntro ? $sIntro." " : "").join(', ',$aCaption);
		if(count($aMissing) > 0) {
			$sCaption .= "<br />Not in photograph: ".join(', ',$aMissing);
		}
		$sCaption .= "</p>";
		return $sCaption;
	}
	
	function get_band_table($iBand) {
		$aBand = $this->xResult->xpath("band[rang=$iBand]");
		if(count($aBand) != 1) {
			return false;
		}
		$aBand = $aBand[0];
		if(!$aBand->ringers) {
			return false;
		}
		else {
			$sTable = "<table class='bandlist'>";
			$sConductor = $aBand->conductor;
			for($i = 1; $i < 13; $i++) {
				$sRinger = $aBand->ringers->xPath('r'.$i);
				$sRinger = $sRinger[0];
				if($sConductor == $sRinger || $sConductor == $i) {
					$sRinger .= " (C)";
				}
				if($sRinger) {
					$sTable .= "<tr><td>$i</td><td>".$sRinger."</td></tr>";
				}
			}
						
		}
		return $sTable;
	}
}

function sort_results($r1, $r2) {
	return strcmp($r2->mark,$r1->mark);
}

function make_ordinal($n) {
	switch($n) {
		case 1:
			return $n."st";
			break;
		case 2:
			return $n."nd";
			break;
		case 3:
			return $n."rd";
		default:
			return $n."th";
	}
}
