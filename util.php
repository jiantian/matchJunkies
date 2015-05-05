<?php
	function imgURL($team) {
		$teamName = ucfirst($team);
		if ($teamName == "Us" OR $teamName == "Unitedstates") {
			$teamName = "USA";
		} 
		if ($teamName == "Korea") {
			$teamName = "Korea_South";
		}
		if ($teamName == "Czech") {
			$teamName = "Czech_Republic";
		}
		if ($teamName == "Ivorycoast") {
			$teamName = "Ivory_Coast";
		}
		if ($teamName == "Saudiarabia") {
			$teamName = "Saudi_Arabia";
		}
		if ($teamName == "Costarica") {
			$teamName = "Costa_Rica";
		}
		$url = "http://www.11v11.com/api/images/flags/".$teamName.".gif";
		return $url;
	}
	
	function teamName($team) {
		$teamName = ucfirst($team);
		if ($team == "unitedstates" OR $team == "us") {
			$teamName = "USA";
		}
		if ($team == "ivorycoast") {
			$teamName = "Ivory Coast";
		}
		if ($team == "saudiarabia") {
			$teamName = "Saudi Arabia";
		}
		if ($team == "costarica") {
			$teamName = "Costa Rica";
		}
		return $teamName;
	}
	
	function playerWiki($player) {
		if ($player == "Luiz Ronaldo") {
			$player = "Ronaldo";
		}
		if ($player == "Kaka") {
			$player = "Kak%C3%A1";
		}
		if ($player == "Pele") {
			$player = "Pel%C3%A9";
		}
		if ($player == "Enrique Omar Sivori") {
			$player = "Omar Sivori";
		}
		if ($player == "Jose Francisco Sanfilippo") {
			$player = "Jos%C3%A9 Sanfilippo";
		}
		if ($player == "Leovegildo Lins da Gama Junior") {
			$player = "Leovegildo Lins da Gama J%C3%BAnior";
		}
		if ($player == "Jan Thomee") {
			$player = "Jan Thom%C3%A9e";
		}
		if ($player == "Josef Pottinger") {
			$player = "Josef P%C3%B6ttinger";
		}
		if ($player == "Jose Aguas") {
			$player = "Jos%C3%A9 %C3%81guas";
		}
		if ($player == "Thomas Muller") {
			$player = "Thomas M%C3%BCller";
		}
		if ($player == "James Rodriguez") {
			$player = "James Rodr%C3%ADguez";
		}
		if ($player == "Gonzalo Higuain") {
			$player = "Gonzalo Higua%C3%ADn";
		}
		$nameArr = explode(" ", $player);
		$firstName = $nameArr[0];
		if (count($nameArr)>1) {	  
			$lastName = end($nameArr);
			$wikiLink = "http://en.wikipedia.org/wiki/".ucfirst($firstName)."_".ucfirst($lastName);
		} else {
			$wikiLink = "http://en.wikipedia.org/wiki/".ucfirst($firstName);
		}
		if ($player == "Robin Van Persie") {
			$wikiLink = "http://en.wikipedia.org/wiki/Robin_van_Persie";
		}
		if ($player == "Alfredo Di Stefano") {
			$wikiLink = "http://en.wikipedia.org/wiki/Alfredo_Di_St%C3%A9fano";
		}
		if ($player == "Frank De Boer") {
			$wikiLink = "http://en.wikipedia.org/wiki/Frank_de_Boer";
		}
		if ($player == "Alessandro Del Piero") {
			$wikiLink = "http://en.wikipedia.org/wiki/Alessandro_Del_Piero";
		}
		if ($player == "Nascimento de Araujo Leonardo") {
            $wikiLink = "http://en.wikipedia.org/wiki/Leonardo_Ara%C3%BAjo";
        }
		if ($player == "Marco van Basten") {
            $wikiLink = "http://en.wikipedia.org/wiki/Marco_van_Basten";
        }
		return $wikiLink;
	}
	
	function playerImg($player) {
		$handle = fopen("players.txt", "r");
		$imgSrc = '';
		while (!feof($handle)) {
			$line = fgets($handle);
			$playerInRecord = explode("\t", $line)[0];
			if ($player == $playerInRecord) {
				$imgSrc = explode("\t", $line)[1];
			}
		}	
		fclose($handle);
		return $imgSrc;		
	}

	function printScorer($scorers, $team1, $team2) {
		$icon = "imgs/scorer.png";
		echo "<h2><img src='$icon'>".teamname($team1)." top scorer against ".teamname($team2)."</h2><br>";
		foreach ($scorers as $scorer) {
			$player = explode("\t", $scorer)[0];
			$goal = intval(explode("\t", $scorer)[1]);
			$src = playerImg($player);
			$url = playerWiki($player);
			echo "<div class='container'><img class='player' src='$src' alt='$player'><p class='text'>
                    <a href='$url'>".$player."</a></p></div>";
		}
		echo "<br><br><br><br><br><br><br>";
		$goalicon = "imgs/goal.png";
		echo "<h3 style='font-family:Futura;'>Goals: ";
		for ($i = 0; $i < $goal; $i++) {
			echo "<img src='$goalicon'>";
		}
		echo "</h3><br>";
	}

	function printSummary($summary) {
		$icon = "imgs/summary.png";
		echo "<h2><img src='$icon'>Match summary</h2>";
		foreach ($summary as $item) {
			if (strpos($item, "won")!=false) {
				echo "<h3 style='font-family:Futura;color:green'>".$item."</h3>";
			} else if (strpos($item, "lost")!=false) {
				echo "<h3 style='font-family:Futura;color:red'>".$item."</h3>";
			} else {
				echo "<h3 style='font-family:Futura;color:gray'>".$item."</h3>";
			}
		}
		echo "<br>";
	}

	function printMatch($match) {
		$icon = "imgs/history.png";
		echo "<h2><img src='$icon'>Match history</h2>";
		echo "<div id='match-container'>";
		echo "<table class='match-list'>";
		echo "<col width='150px'>";
		echo "<col width='200px'>";
		echo "<col width='70px'>";
		echo "<col width='90px'>";
		echo "<col width='300px'>";
		echo "<thead>";
		echo "<tr style='background-color: black; color: white;'><th>Date</th><th>Match</th><th>Result</th><th>Score</th><th>Competition</th></tr>";
		echo "</thead>";
		echo "<tbody>";
		$handle = fopen("links.txt", "r");
		while (!feof($handle)) {
			$links[] = fgets($handle, 4096);
		}
		fclose($handle);
		$i = 0;
		foreach ($match as $item) {
			$str = explode("\t",$item);
			if ($i % 2 == 0) {
				echo "<tr class='even'>";
			} else {
				echo "<tr class='odd'>";
			}
			$col = 0;
			foreach($str as $elem) {
				if ($elem == "L") {
					echo "<td style='color:red;'>".$elem."</td>";
				} else if ($elem == "W") {
					echo "<td style='color:green;'>".$elem."</td>";
				} else if ($elem == "D") {
					echo "<td style='color:gray;'>".$elem."</td>";
				} else if ($col==1) {
					$link = $links[$i];
					echo "<td><a href='$link'>".$elem."</a></td>";
				} else {
					echo "<td>".$elem."</td>";
				}
				$col++;
			}
			echo "</tr>";
			$i++;
		}
		echo "</tbody>";
		echo "</table>";
		echo "</div>";
		echo "<br><br><br>";
	}

	function printWin($win) {
		$icon = "imgs/win.png";
		echo "<h2><img src='$icon'>".$win[0]."</h2>";
		echo "<h3 style='font-family:Century Gothic;text-indent:30px;'><i>".$win[1]."</i></h3>";
		$length = count($win);
		echo "<ul>";
		for ($i=2; $i<$length; $i++) {
			echo "<li><h4 style='font-family:Arial;color:green;'>".$win[$i]."</h4></li>";
		}
		echo "</ul>";
		echo "<br>";
	}

	function printLoss($loss) {
		$icon = "imgs/loss.jpg";
        echo "<h2><img src='$icon'>".$loss[0]."</h2>";
        echo "<h3 style='font-family:Century Gothic;text-indent:30px;'><i>".$loss[1]."</i></h3>";
        $length = count($loss);
        echo "<ul>";
        for ($i=2; $i<$length; $i++) {
            echo "<li><h4 style='font-family:Arial;color:red;'>".$loss[$i]."</h4></li>";
        }
        echo "</ul>";
        echo "<br>";
    }

	function printHighscore($highScore, $match) {
		$icon = "imgs/highScore.png";
		echo "<h2><img src='$icon'>".$highScore[0]."</h2>";
		$length = count($highScore);
		echo "<div id='match-container'>";
        echo "<table class='match-list'>";
        echo "<col width='150px'>";
        echo "<col width='200px'>";
        echo "<col width='70px'>";
        echo "<col width='90px'>";
        echo "<col width='300px'>";
        echo "<thead>";
        echo "<tr style='background-color: black; color: white;'><th>Date</th><th>Match</th><th>Result</th><th>Score</th><th>Competition</th></tr>";
        echo "</thead>";
        echo "<tbody>";
		$handle = fopen("links.txt", "r");
        while (!feof($handle)) {
            $links[] = fgets($handle, 4096);
        }
        fclose($handle);
		$len_match = count($match);
		$count = 0;
        for ($i = 1; $i < $length; $i++) {
			for ($j = 0; $j < $len_match; $j++) {
				if ($highScore[$i] == $match[$j]) {
					$index = $j;
					break;
				}
			}
            $str = explode("\t",$highScore[$i]);
            if ($count % 2 == 0) {
                echo "<tr class='even'>";
            } else {
                echo "<tr class='odd'>";
            }
            $count++;
			$col = 0;
            foreach($str as $elem) {
                if ($elem == "L") {
                    echo "<td style='color:red;'>".$elem."</td>";
                } else if ($elem == "W") {
                    echo "<td style='color:green;'>".$elem."</td>";
                } else if ($elem == "D") {
                    echo "<td style='color:gray;'>".$elem."</td>";
				} else if ($col==1) {
                    $link = $links[$index];
                    echo "<td><a href='$link'>".$elem."</a></td>";
                } else {
                    echo "<td>".$elem."</td>";
                }
				$col++;
            }
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "<br><br><br>";
	}
?>
