<?php
require_once __DIR__ . '/../vendor/autoload.php';

use SportlinkClubData\ClubData;
use SportlinkClubData\Team;
use SportlinkClubData\Match;

$key = 'JOKcIznJ9m';
$sportlink = new ClubData($key);

/*
$club = $sportlink->getClub();
echo $club->clubnaam, $club->thuisbroekkleur, $club->oprichtingsdatum->format('d-M-Y'), $club->getVisitingAddress()->plaats.'<br/>';
foreach ($sportlink->getLeagues() as $league) {
	echo "> League: " . $league->teamcode, "-", $league->teamnaam, "-", $league->competitienaam, "<br/>";
	$team = $league->getTeam();
	echo ">> Team: " . $team->teamnaam, "-", $team->geslacht,"-", $team->categorie, "<br/>";
	$leagueresult = $league->getMatchResults(true, 50, -10);
	foreach ($leagueresult as $leaguematch) {
		echo ">>> Uitslag: ", " - ", $leaguematch->wedstrijdcode, " - ", $leaguematch->wedstrijd, "-", $leaguematch->wedstrijddatum->format('d-M-Y'), $leaguematch->uitslag, "<br/>";
	}
	$leagueschedule = $league->getMatchSchedule(true, 10);
	foreach ($leagueschedule as $leaguematch) {
		echo ">>> Programma: ", " - ", $leaguematch->wedstrijdcode, " - ", $leaguematch->wedstrijd, "-", $leaguematch->wedstrijddatum->format('d-M-Y'), $leaguematch->getMatch()->getOfficials()->vereningsscheidsrechter, "<br/>";
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
*/

$team = new Team($sportlink, 133473, -1);

echo $team->teamnaam, "-", $team->geslacht,"-", $team->categorie, "<br/>";

/*
$match = new Match($sportlink, 10769519);
echo "Match: ", $match->wedstijdnummerintern, "-", $match->wedstrijddatum->format('d-M-Y'),"-", $match->thuisteam, "-", $match->uitteam, 
		"-", $match->thuisscore,":", $match->uitscore, "<br/>";
echo "> Facilities:", $match->getFacilities()->naam, $match->getFacilities()->plaats, "<br/>";
echo "> Uitteam: ", $match->getAwayTeamClub()->code,"-clubnaam: ", $match->getAwayTeamClub()->naam,"-site: ", $match->getAwayTeamClub()->website, 
		" - perc gewonnen: ",$match->getStatisticsAwayTeam()->percentagegewonnen, "<br/>";
echo "> Thuisteam: ", $match->getHomeTeamClub()->code,"-clubnaam: ", $match->getHomeTeamClub()->naam,"-site: ", $match->getHomeTeamClub()->website, 
		" - perc gewonnen: ", $match->getStatisticsHomeTeam()->percentagegewonnen, "<br/>";
foreach ($match->getOfficials()->getOfficials() as $official) {
	echo "> Officials: ",$official->relatiecode, "-", $official->officialnaam, "-", $official->officialomschrijving,"<br/>";
}
echo "> Officials club: ", $match->getOfficials()->verenigingsscheidsrechtercode, "-", $match->getOfficials()->vereningsscheidsrechter, "-", $match->getOfficials()->overigeofficialcode,
		"-",$match->getOfficials()->overigeofficial,  "<br/>";
foreach ($match->getPlayersHomeTeam() as $player) {
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
foreach ($match->getPastResults() as $pastresult) {
	echo "> Match past result: ", $pastresult->seizoen, " - ", $pastresult->wedstrijd, " - ", $pastresult->datum,  " - ", $pastresult->uitslag, "<br/>";
}

foreach ($sportlink->getSchedule() as $match) {
	echo "Clubschedule-match: ",  $match->wedstrijdcode, " - ", $match->wedstrijddatum->format('d-M-Y H:i'), " - ", $match->wedstrijd, " - ", $match->teamvolgorde, " - ", 
		$match->scheidsrechters, "- sportveld - ", $match->getMatch()->getFacilities()->naam, "<br/>";
}

 foreach ($sportlink->getOptionsSortorder() as $option) {
	echo "Options sortorder: ", $option->sorteervolgorde, " - ", $option->omschrijving, "<br/>";
}
foreach ($sportlink->getOptionsLeaguePeriod() as $option) {
	echo "Options leagueperiod: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($sportlink->getOptionsLeagueType() as $option) {
	echo "Options leaguetype: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($sportlink->getOptionsDayType() as $option) {
	echo "Options daytype: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($sportlink->getOptionsInvoiceStatus() as $option) {
	echo "Options invoicestatus: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($sportlink->getOptionsSex() as $option) {
	echo "Options sex: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($sportlink->getOptionsAgeCategory() as $option) {
	echo "Options agecategory: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($sportlink->getOptionsMatchType() as $option) {
	echo "Options matchtype: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($sportlink->getOptionsTeamRole() as $option) {
	echo "Options teampersonrole: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}
foreach ($sportlink->getOptionsTeamType() as $option) {
	echo "Options teamtype: ", $option->waarde, " - ", $option->omschrijving, "<br/>";
}

foreach ($sportlink->getResults(21,-15, null, null, null, null, 20) as $match) {
	echo "Clubresults-match: ",  $match->wedstrijdcode, " - ", $match->wedstrijddatum->format('d-M-Y H:i'), " - ", $match->wedstrijd, " - ", $match->uitslag, " - ",
	$match->sportomschrijving, "- sportveld - ", $match->accommodatie, "<br/>";
}
foreach ($sportlink->getAnniversaries(100) as $anniversary) {
    if (isset($anniversary->geboortedatum)) $gebdat = $anniversary->geboortedatum->format('d-M'); else $gebdat = 'onbekend';
	echo "Anniversary: ", $gebdat, "::", $anniversary->volledigenaam, "::", $anniversary->leeftijd, "::", $anniversary->nieuweleeftijd, "<br/>";
}
foreach ($sportlink->getCancellations() as $cancellation) {
	echo "Cancellation: ", $cancellation->wedstrijd, "::", $cancellation->aanvangstijd, " - ", $cancellation->status, "<br/>";
}
foreach ($sportlink->getTeams() as $k=>$team) {
	echo "> Team: ", $k, "=", $team->teamcode, "-", $team->teamnaam, "-", $team->geslacht, "<br/>";
	foreach ($team->getLeagues() as $poulecode=>$league) {
		echo ">> League: ", $poulecode, ":", $league->teamcode, ":", $league->teamnaam, ":", $league->competitienaam, "<br/>";
		foreach ($league->getPeriods() as $period) {
		    echo ">>> Period: ", $period->poulecode, ":", $period->huidig, ":", $period->omschrijving, ":", $period->waarde, "<br/>";
		}
	}
}

*/

/*
 foreach ($sportlink->getLeagues(true) as $poulecode=>$league) {
	echo ">> League: ", $poulecode, ":", $league->teamcode, ":", $league->teamnaam, ":", $league->competitienaam, "<br/>";
}
*/

foreach ($sportlink->getTeams() as $team) {
	foreach ($team->getLeagues() as $league) {
		echo ">> Teams regulier|current: ", $team->teamnaam, ":", $league->teamcode, "poulecode: ", $league->poulecode, ":", $league->competitiesoort, ":", $league->competitienaam, "<br/>";
	}
}


foreach ($sportlink->getTeams() as $team) {
	foreach ($team->getLeagues(true) as $league) {
		echo ">> Teams regulier|all: ", $team->teamnaam, ":", $league->teamcode, ":", $league->competitiesoort, ":", $league->competitienaam, "<br/>";
	}
}

foreach ($sportlink->getTeams() as $team) {
	foreach ($team->getLeagues(false, false) as $league) {
		echo ">> Teams all|current: ", $team->teamnaam, ":", $league->teamcode, ":", $league->competitiesoort, ":", $league->competitienaam, "<br/>";
	}
}
foreach ($sportlink->getTeams() as $team) {
	foreach ($team->getLeagues() as $league) {
	    echo ">> Teams all|all: ", $team->teamnaam, ":", $team->teamnaam_full, ":", $league->teamcode, ":", $league->competitiesoort, ":", $league->competitienaam, "<br/>";
	}
}


foreach ($sportlink->getTeams() as $team) {
    echo ">> Teams club: ", $team->teamcode, ":", $team->teamnaam, ":", $team->teamnaam_full, ":", $team->leeftijdscategorie, ":", $team->isPopulated()?"details present!":"niet gepopuleerd", ":", $team->categorie, "<br/>";
}

foreach ($sportlink->getTeams(true) as $team) {
    echo ">> Teams club: ", $team->teamcode, ":", $team->teamnaam, ":", $team->teamnaam_full, ":", $team->leeftijdscategorie, ":", $team->isPopulated()?"details present!":"niet gepopuleerd", ":", $team->categorie, "<br/>";
}

foreach ($sportlink->getSchedule(8,0,null,true) as $match) {
    echo "Clubschedule-match: ",  $match->wedstrijdcode, " - ", $match->wedstrijddatum->format('d-M-Y H:i'), " - ", $match->wedstrijd, " - ", $match->teamvolgorde, " - ",
    $match->scheidsrechters, "- sportveld - ", $match->getMatch()->getFacilities()->naam, "<br/>";
}

foreach ($sportlink->getCommissions() as $commission) {
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