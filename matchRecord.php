<html>
	<head>
		<title>Match Junkies | Search result</title>

		<style>

			@font-face {
 				font-family: Exo-Regular;
    			src: url(fonts/Exo-Regular.otf);
			}

			body {
				background-color: #b0c4de;
			}

			.header{
				position: absolute;
				top: calc(10% - 35px);
				left: calc(25% - 255px);
				z-index: 2;
			}

			.header div{
				float: left;
				color: #fff;
				font-family: 'Exo', sans-serif;
				font-size: 25px;
				font-weight: 200;
			}

			.header div span{
				color: #5379fa !important;
			}
			
			.result {
				position: absolute;
				top: 100px;
				left: calc(25% - 255px);
			}
			
			.result h1 {
				font-family: Helvetica, Lucida Grande, sans-serif;
			}
			
			.player {
				//max-width:70px;
				//max-height:70px;
				width:65px;
				height:60px;
			}
			
			.container {
				width: 65px;
				float: left;
				margin: 6px 6px 6px 6px;
			}
			
			.text {
				text-align: center;
			}

			h2 {
				font-family: Exo-Regular, sans-serif;
				color: 330099;
			}

			h2 img {
				width: 30px;
				height: 30px;
				margin: 6px 6px 0px 6px;
			}

			table {
   				border: 1px solid black;
				table-layout:fixed;
			} 

			th, td {
				text-align: center;
				font-family: Candara, Garamond, sans-serif;
				font-size: 20px;
			}

			.odd {
				background-color: FFFFFF;
			}

			.even {
				background-color: FFFFCC;
			}
		</style>
	</head>

	<body>
		<!--
		<form action="matchRecord.php" method="post">
			<p>Enter Team 1: <input type="text" name="team1" /></p>
			<p>Enter Team 2: <input type="text" name="team2" /></p>
			<p><input type="submit" /></p>
		</form>
		-->
		
		<div class='header'>
			<div><a href="https://web.engr.illinois.edu/~jtian4/matchjunkies/">Return to search</a></div>
		</div>

		<div class='result'>
		<?php
			require_once 'util.php';
			$team1 = htmlspecialchars($_POST['team1']);
			$team2 = htmlspecialchars($_POST['team2']);
			shell_exec('python search.py '.$team1.' '.$team2.'>result.txt');
			$imgURL1 = imgURL($team1);
			$imgURL2 = imgURL($team2);
			echo "<h1><img src='$imgURL1'>"."   ".teamName($team1)." national team record  
				vs.   ".teamName($team2)."   <img src='$imgURL2'></h1><br>";

			$handle = fopen('result.txt', 'r');
			while (!feof($handle)) {
				$lines[] = fgets($handle, 4096);
			}
			fclose($handle);

			$summary = array();
			$match = array();
			$scorer1 = array();
			$scorer2 = array();
			$win = array();
			$loss = array();
			$highScore = array();
			$parsing = false;
			$parsing1 = false;
			$parsing2 = false;
			$parse_win = false;
			$parse_loss = false;
			$parse_highScore = false;
			$pos = strpos($lines[1], "national");
			$hostteam = trim(substr($lines[1], 0, $pos));
			$team = "";
			foreach ($lines as $line) {
				$game = explode(" ", $line)[0];
				if ($game == "Games") {
					array_push($summary, $line);
				}

				$date = explode("\t", $line)[0];
				if (trim($line) == "") {
					$parsing = false;
				}
				if ($parsing) {
					array_push($match, $line);
				}
				if ($date == "Date") {
                    $parsing = true;
                }

				if (strpos($line, "Top") != false) {
					$team = trim(substr($line, 0, strpos($line, "Top")));
				}
				if ($team == $hostteam) {
					if (trim($line) == "") {
						$parsing1 = false;
						$team = "";
					}
					if ($parsing1) {
						array_push($scorer1, $line);
					}
					if (strpos($line, "Top") != false) {
						$parsing1 = true;
					}
				}
				if ($team != $hostteam AND strlen($team) > 0){
					if (trim($line) == "") {
                        $parsing2 = false;
                        $team = "";
                    }   
                    if ($parsing2) {
                        array_push($scorer2, $line);
                    }   
                    if (strpos($line, "Top") != false) {
                        $parsing2 = true;
                    }
				}

				$out = explode(" ", $line)[1];
				if ($out == "winning") {
					$parse_win = true;
				}
				if (trim($line) == "") {
                    $parse_win = false;
                }
                if ($parse_win) {
                    array_push($win, $line);
                }
				if ($out == "losing") {
                    $parse_loss = true;
                }
                if (trim($line) == "") {
                    $parse_loss = false;
                }
                if ($parse_loss) {
                    array_push($loss, $line);
                }

				$out = explode(" ", $line)[1];
				if ($out == "scoring") {
                    $parse_highScore = true;
                }
                if (trim($line) == "==========================================================================") {
                    $parse_highScore = false;
                }
                if ($parse_highScore) {
                    array_push($highScore, $line);
                }
			}

			printSummary($summary);
			printMatch($match);
			printWin($win);
			printLoss($loss);
			printHighscore($highScore, $match);
			printScorer($scorer1, $team1, $team2);
			printScorer($scorer2, $team2, $team1);
		?>
		</div>
	</body>
</html>
