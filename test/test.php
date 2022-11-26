<?php
require_once __DIR__ . '/../vendor/autoload.php';


use SportlinkClubData\DataManager;
use SportlinkClubData\ClubsManager;
use SportlinkClubData\ClubManager;
use SportlinkClubData\Team;
use SportlinkClubData\MatchDetail;



/* ***INITIALIZE MANAGERS*** */
$key = 'JOKcIznJ9m';
$key2 = "dtEQ1RQ4jc";
//$datamanager = new DataManager($key);
//$clubmanager = new ClubManager($datamanager); 

$clubsmanager = new ClubsManager([$key, $key2]);
$clubmanager = $clubsmanager->getClubManager(0);

/* ***GET CLUB VIA CLUBMANAGER*** */


$club = $clubmanager->getClub();
echo $club->clubnaam, $club->thuisbroekkleur, $club->oprichtingsdatum, $club->getVisitingAddress()->plaats.'<br/>';

# ***GET LEAGUES VIA CLUBMANAGER*** 
foreach ($clubmanager->getLeagues() as $t) {
	foreach ($t as $league) {
		echo "** Competitie **";
		echo "> League: ", $league->client_id, "-", $league->teamcode, "-", $league->teamnaam, "-", $league->competitienaam, "<br/>";
		$team = $league->getTeam();
		echo ">> Team: ", "-", $team->teamnaam, "-", $team->geslacht,"-", $team->categorie, "<br/>";
		$leagueresult = $league->getMatchResults(true, 50, -10);
		foreach ($leagueresult as $leaguematch) {
			echo ">>> Uitslag: ", $leaguematch->client_id, " - ", $leaguematch->wedstrijdcode, " - ", $leaguematch->wedstrijd, "-", $leaguematch->wedstrijddatum->format('d-M-Y'), "- eindstand: ", $leaguematch->uitslag, "<br/>";
		}
		$leagueschedule = $league->getMatchSchedule(true, 10);
		foreach ($leagueschedule as $leaguematch) {
			echo ">>> Programma: ", $leaguematch->client_id, " - ", $leaguematch->wedstrijdcode, " - ", $leaguematch->wedstrijd, "-", $leaguematch->wedstrijddatum->format('d-M-Y'), $leaguematch->getMatchDetail()->getReferee(), "<br/>";
		}
		foreach ($league->getRanking() as $position) {
			echo ">>>> Stand: ", $position->positie, " ", $position->teamnaam, " ", $position->gespeeldewedstrijden, " ", $position->doelpuntenvoor, " ", $position->doelpuntentegen, " ", $position->punten, " ", $position->eigenteam, "<br/>";
		}
		foreach ($league->getPeriods() as $period) {
			echo ">>> Periodes: ", $period->waarde, " - ", $period->omschrijving, " - ", $period->huidig, "<br/>"; 
			foreach ($period->getRanking() as $position) {
				echo ">>>> Periodestand: ", $position->positie, " ", $position->teamnaam, " ", $position->aantalwedstrijden, " ", $position->doelpuntenvoor, " ", $position->tegendoelpunten, " ", $position->totaalpunten, "<br/>";
			}
		}
	}
}

/*
$team = new Team($datamanager, 133473, -1);

echo $team->teamnaam, "-", $team->geslacht,"-", $team->categorie, "<br/>";


$amatch = new MatchDetail($datamanager, 15376754);
echo "Match: ", $amatch->wedstijdnummerintern, "-", $amatch->wedstrijddatum->format('d-M-Y'),"-", $amatch->thuisteam, "-", $amatch->uitteam, 
		"-", $amatch->thuisscore,":", $amatch->uitscore, "<br/>";
echo "> Facilities:", $amatch->getFacilities()->naam, $amatch->getFacilities()->plaats, "<br/>";


echo "> Uitteam: ", $amatch->getAwayTeamClub()->code,"-clubnaam: ", $amatch->getAwayTeamClub()->naam,"-site: ", $amatch->getAwayTeamClub()->website, 
		" - perc gewonnen: ",$amatch->getStatisticsAwayTeam()->percentagegewonnen, "<br/>";
echo "> Thuisteam: ", $amatch->getHomeTeamClub()->code,"-clubnaam: ", $amatch->getHomeTeamClub()->naam,"-site: ", $amatch->getHomeTeamClub()->website, 
		" - perc gewonnen: ", $amatch->getStatisticsHomeTeam()->percentagegewonnen, "<br/>";

foreach ($amatch->getPlayersHomeTeam() as $player) {
	echo "> Match player: ", $player->relatiecode, " - ", $player->naam, " - ", $player->functie, " - ", $player->rol, " - ", $player->email, "<br/>";
	if (isset($player->foto)) {
		echo '<div><img src="data:image/png;base64,', $player->foto, '" alt="Foto" /></div>';
	}
}

foreach ($team->getPlayers(true) as $player) {
    echo "> Team player: ", $player->relatiecode, " - ", $player->naam, " - ", $player->functie, " - ", $player->rol, " - ", $player->email, " - Private:", $player->private, "<br/>";
	if (isset($player->foto)) {
		echo '<div><img src="data:image/png;base64,', $player->foto, '" alt="Foto" /></div>';
	}
}
foreach ($amatch->getPastResults() as $pastresult) {
	echo "> Match past result: ", $pastresult->seizoen, " - ", $pastresult->wedstrijd, " - ", $pastresult->datum,  " - ", $pastresult->uitslag, "<br/>";
}

foreach ($clubmanager->getSchedule(100) as $amatch) {
	$matchdetail=$amatch->getMatchDetail();
	echo "Clubschedule-match: ",  $amatch->wedstrijdcode, " - ", $amatch->wedstrijddatum->format('d-M-Y H:i'), " - ", $amatch->wedstrijd, " - ", $amatch->teamvolgorde, " - ", 
		$amatch->scheidsrechters, "- sportveld - ", $matchdetail->getFacilities()->naam, "<br/>";

	foreach ($matchdetail->getMatchOfficials() as $official) {
		echo "> KNVB Officials: ",$official->relatiecode, "-", $official->getOfficialPrivate()? "***":$official->officialnaam, "-", $official->officialomschrijving,"<br/>";
	}
	echo "> Officials club: ", $matchdetail->getClubOfficials()->verenigingsscheidsrechtercode, "-", ($matchdetail->getClubOfficials()->getRefereePrivate()? "***": $matchdetail->getClubOfficials()->verenigingsscheidsrechter), "-", $matchdetail->getClubOfficials()->overigeofficialcode,
	"-",($matchdetail->getClubOfficials()->getOtherOfficialPrivate()? "***": $matchdetail->getClubOfficials()->overigeofficial),  "<br/>";
 
 	echo "> Referee: ", $matchdetail->getReferee(), "- Scheidsrechter: ", ($amatch->getRefereePrivate()? "***": $amatch->scheidsrechter),  "<br/>";
	echo "> Via match scheidsrechter: ", ($amatch->getRefereePrivate()? "WilNietZeggen": $amatch->getReferee()),"<br/>";
}
foreach ($clubmanager->getOptionsSortorder() as $option) {
	echo "Options sortorder: ", $option->sorteervolgorde, " - ", $option->omschrijving, "<br/>";
}
foreach ($clubmanager->getOptionsLeaguePeriod() as $option) {
	echo "Options leagueperiod: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($clubmanager->getOptionsLeagueType() as $option) {
	echo "Options leaguetype: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($clubmanager->getOptionsDayType() as $option) {
	echo "Options daytype: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($clubmanager->getOptionsInvoiceStatus() as $option) {
	echo "Options invoicestatus: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($clubmanager->getOptionsSex() as $option) {
	echo "Options sex: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($clubmanager->getOptionsAgeCategory() as $option) {
	echo "Options agecategory: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($clubmanager->getOptionsMatchType() as $option) {
	echo "Options matchtype: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($clubmanager->getOptionsTeamRole() as $option) {
	echo "Options teampersonrole: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($clubmanager->getOptionsTeamType() as $option) {
	echo "Options teamtype: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}

foreach ($clubmanager->getResults(21,-15, null, null, null, null, 20) as $amatch) {
	echo "Clubresults-match: ",  $amatch->wedstrijdcode, " - ", $amatch->wedstrijddatum->format('d-M-Y H:i'), " - ", $amatch->wedstrijd, " - ", $amatch->uitslag, " - ",
	$amatch->sportomschrijving, "- sportveld - ", $amatch->accommodatie, "<br/>";
}
foreach ($clubmanager->getAnniversaries(100) as $anniversary) {
    if (isset($anniversary->geboortedatum)) $gebdat = $anniversary->geboortedatum->format('d-M'); else $gebdat = 'onbekend';
	echo "Anniversary: ", $gebdat, "::", $anniversary->volledigenaam, "::", $anniversary->leeftijd, "::", $anniversary->nieuweleeftijd, "<br/>";
}
foreach ($clubmanager->getCancellations() as $cancellation) {
	echo "Cancellation: ", $cancellation->wedstrijd, "::", $cancellation->aanvangstijd, " - ", $cancellation->status, "<br/>";
}


foreach ($clubmanager->getTeams() as $k=>$team) {
	echo "> Team: ", $k, "=", $team->teamcode, "-", $team->teamnaam_full, "-", $team->geslacht, "<br/>";
	foreach ($team->getLeagues() as $poulecode=>$league) {
		echo ">> League: ", $poulecode, ":", $league->teamcode, ":", $league->teamnaam, ":", $league->competitienaam, "<br/>";
		foreach ($league->getPeriods() as $period) {
		    echo ">>> Period: ", $period->poulecode, ":", $period->huidig, ":", $period->omschrijving, ":", $period->waarde, "<br/>";
		}
	}
}


foreach ($clubmanager->getLeagues(true) as $poulecode=>$league) {
	echo ">> League: ", $poulecode, ":", $league->teamcode, ":", $league->teamnaam, ":", $league->competitienaam, "<br/>";
}

foreach ($clubmanager->getTeams() as $team) {
	foreach ($team->getLeagues() as $league) {
		echo ">> Teams regulier|current: ", $team->teamnaam, ":", $league->teamcode, "poulecode: ", $league->poulecode, ":", $league->competitiesoort, ":", $league->competitienaam, "<br/>";
	}
}


foreach ($clubmanager->getTeams() as $team) {
	foreach ($team->getLeagues(true) as $league) {
		echo ">> Teams regulier|all: ", $team->teamnaam, ":", $league->teamcode, ":", $league->competitiesoort, ":", $league->competitienaam, "<br/>";
	}
}

foreach ($clubmanager->getTeams() as $team) {
	foreach ($team->getLeagues(false, false) as $league) {
		echo ">> Teams all|current: ", $team->teamnaam, ":", $league->teamcode, ":", $league->competitiesoort, ":", $league->competitienaam, "<br/>";
	}
}
foreach ($clubmanager->getTeams() as $team) {
	foreach ($team->getLeagues() as $league) {
	    echo ">> Teams all|all: ", $team->teamnaam, ":", $team->teamnaam_full, ":", $league->teamcode, ":", $league->competitiesoort, ":", $league->competitienaam, "<br/>";
	}
}


foreach ($clubmanager->getTeams() as $team) {
    echo ">> Teams club: ", $team->teamcode, ":", $team->teamnaam, ":", $team->teamnaam_full, ":", $team->leeftijdscategorie, ":", $team->isPopulated()?"details present!":"niet gepopuleerd", ":", $team->categorie, "<br/>";
}

foreach ($clubmanager->getTeams(true) as $team) {
    echo ">> Teams club: ", $team->teamcode, ":", $team->teamnaam, ":", $team->teamnaam_full, ":", $team->leeftijdscategorie, ":", $team->isPopulated()?"details present!":"niet gepopuleerd", ":", $team->categorie, "<br/>";
}

foreach ($clubmanager->getSchedule(8,0,null,true) as $amatch) {
    echo "Clubschedule-match: ",  $amatch->wedstrijdcode, " - ", $amatch->wedstrijddatum->format('d-M-Y H:i'), " - ", $amatch->wedstrijd, " - ", $amatch->teamvolgorde, " - ",
    $amatch->scheidsrechters, "- sportveld - ", $amatch->getMatch()->getFacilities()->naam, "<br/>";
}

foreach ($clubmanager->getCommissions() as $commission) {
    echo ">> Commission: ", $commission->commissiecode, ":", $commission->commissienaam, ":", $commission->omschrijving, ":", $commission->telefoon, ":", $commission->email, "<br/>";
    if (isset($commission->foto)) {
        echo '<div><img src="data:image/png;base64,', $commission->foto, '" alt="Foto" /></div>';
    }
    foreach ($commission->getMembers() as $member) {
        echo ">> Member: ", $member->lid , ":", $member->functie, ":", $member->email, ":", $member->informatie, ":", $member->startdatum, ":", $member->einddatum, "<br/>";
        if (isset($member->foto)) {
            echo '<div><img src="data:image/png;base64,', $member->foto, '" alt="Foto" /></div>';
        }
    }
    
}
*/

$clubs = $clubsmanager->getClubs();
foreach ($clubs as $club) {
	echo $club->client_id, " - ", $club->clubnaam, $club->thuisbroekkleur, $club->oprichtingsdatum, $club->getVisitingAddress()->plaats.'<br/>';
}

/*
# ***GET LEAGUES VIA CLUBSMANAGER***
foreach ($clubsmanager->getLeagues() as $t) {
	foreach ($t as $league) {
		echo "** Competitie **", "<br/>";
		echo "> League: " . $league->teamcode, "-", $league->teamnaam, "-", $league->competitienaam, "<br/>";
		$team = $league->getTeam();
		echo ">> Team: " . $team->teamnaam_full, "-", $team->geslacht,"-", $team->categorie, "<br/>";
		$leagueresult = $league->getMatchResults(true, 50, -10);
		foreach ($leagueresult as $leaguematch) {
			echo ">>> Uitslag: ", " - ", $leaguematch->wedstrijdcode, " - ", $leaguematch->wedstrijd, "-", $leaguematch->wedstrijddatum->format('d-M-Y'), "- eindstand: ", $leaguematch->uitslag, "<br/>";
		}
		$leagueschedule = $league->getMatchSchedule(true, 10);
		foreach ($leagueschedule as $leaguematch) {
			echo ">>> Programma: ", " - ", $leaguematch->wedstrijdcode, " - ", $leaguematch->wedstrijd, "-", $leaguematch->wedstrijddatum->format('d-M-Y'), $leaguematch->getMatchDetail()->getReferee(), "<br/>";
		}
		foreach ($league->getRanking() as $position) {
			echo ">>>> Stand: ", $position->positie, " ", $position->teamnaam, " ", $position->gespeeldewedstrijden, " ", $position->doelpuntenvoor, " ", $position->doelpuntentegen, " ", $position->punten, " ", $position->eigenteam, "<br/>";
		}
		foreach ($league->getPeriods() as $period) {
			echo ">>> Periodes: ", $period->waarde, " - ", $period->omschrijving, " - ", $period->huidig, "<br/>";
			foreach ($period->getRanking() as $position) {
				echo ">>>> Periodestand: ", $position->positie, " ", $position->teamnaam, " ", $position->aantalwedstrijden, " ", $position->doelpuntenvoor, " ", $position->tegendoelpunten, " ", $position->totaalpunten, "<br/>";
			}
		}
	}
}


foreach ($clubsmanager->getTeams() as $team) {
	echo "> Team: " . $team->teamnaam_full, "-", $team->geslacht,"-", $team->categorie, "<br/>";
}

foreach ($clubsmanager->getSchedule(100) as $amatch) {
	$matchdetail=$amatch->getMatchDetail();
	echo "Clubschedule-match: ",  $amatch->wedstrijdcode, " - ", $amatch->wedstrijddatum->format('d-M-Y H:i'), " - ", $amatch->wedstrijd, " - ", $amatch->teamvolgorde, " - ",
	$amatch->scheidsrechters, "- sportveld - ", $matchdetail->getFacilities()->naam, "<br/>";
	
	foreach ($matchdetail->getMatchOfficials() as $official) {
		echo "> KNVB Officials: ",$official->relatiecode, "-", $official->getOfficialPrivate()? "***":$official->officialnaam, "-", $official->officialomschrijving,"<br/>";
	}
	echo "> Officials club: ", $matchdetail->getClubOfficials()->verenigingsscheidsrechtercode, "-", ($matchdetail->getClubOfficials()->getRefereePrivate()? "***": $matchdetail->getClubOfficials()->verenigingsscheidsrechter), "-", $matchdetail->getClubOfficials()->overigeofficialcode,
	"-",($matchdetail->getClubOfficials()->getOtherOfficialPrivate()? "***": $matchdetail->getClubOfficials()->overigeofficial),  "<br/>";
	
	echo "> Referee: ", $matchdetail->getReferee(), "- Scheidsrechter: ", ($amatch->getRefereePrivate()? "***": $amatch->scheidsrechter),  "<br/>";
	echo "> Via match scheidsrechter: ", ($amatch->getRefereePrivate()? "WilNietZeggen": $amatch->getReferee()),"<br/>";
}
*/
foreach ($clubsmanager->getResults(50) as $amatch) {
	echo "Clubresults-match: ",  $amatch->client_id, " - ", $amatch->wedstrijdcode, " - ", $amatch->wedstrijddatum->format('d-M-Y H:i'), " - ", $amatch->wedstrijd, " - ", " - ",
	$amatch->uitslag, "<br/>";
}

foreach ($clubsmanager->getTeams() as $team) {
	echo ">> Teams club: ", $team->teamcode, ":", $team->teamnaam, ":", $team->teamnaam_full, ":", $team->leeftijdscategorie, ":", $team->isPopulated()?"details present!":"niet gepopuleerd", ":", $team->categorie, "<br/>";
	echo "<img src=\"data:image/png;base64," . $team->populate()->teamfoto . "\" />";
}

