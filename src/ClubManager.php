<?php
namespace SportlinkClubData;


/**
 * Sportlink ClubManager class
 *
 */
class ClubManager extends ClubDataItem
{

	/** @var Club $club  */
	protected $club;

	/** @var string $key	clientID sportlink  */
	protected $key;

	/** @var League[]  */
	protected $leagues = [];
	
	/** @var Team[]  */
	protected $teams = [];
	
	/** @var Anniversary[]  */
	protected $anniversaries = [];
	
	/** @var boolean */
	protected $regularteamsonly = false;

	/** @var boolean */
	protected $allperiods = true;
	
	/** @var boolean */
	protected $teamfulldata = false;
	
	/** @var Commission[]  */
	protected $commissions = [];
	
	/**
	 * Get static data of the club
	 *
	 * @return Club
	 */
	public function getClub()
	{
		if ($this->club) {
			return $this->club;
		}

		$this->club = new Club($this->api);
		return $this->club;
	}
	
	/**
	 * Get a list of leagues in which teams of the club are participating
	 * 
	 * @param boolean $regularonly only teams in a *regular* competition are returned
	 * @param boolean $allperiods all periods are returned, else only current period is returned
	 * @return League[][] array of teams, keyed by *teamcode*, holding array of leagues, keyed by *poulecode*
	 */
	public function getLeagues($regularonly=false, $allperiods=true)
	{
		// for efficiency, no need to request teams when already available
		if ($this->leagues && $this->regularteamsonly == $regularonly && $this->allperiods == $allperiods) {
			return $this->leagues;
		}
	
		$params=[];
		if ($allperiods) {
			$params['competitieperiode']='ALLES';
		}
		if ($regularonly) { 
			$params['competitiesoort'] = 'regulier'; 
		}
		$response = $this->api->request('teams', $params);
	
		$this->leagues = array();
		foreach($response as $item){
			/** @var League $league */
			$league = $this->api->map($item, new League($this->api));
			$league->clubindex = $this->api->getClubsManager()->getClubIndex($league->api->getKey());
			if (!(empty($league->teamcode) || empty($league->poulecode))) {		# some poules are empty for some reason
				$this->leagues[$league->teamcode][$league->poulecode] = $league;
			}
		}
		$this->regularteamsonly = $regularonly;
		$this->allperiods = $allperiods;
		
		return  $this->leagues;
	}

	/**
	 * Get a list of teams of the club
	 * 
	 * @param boolean $full Should Team objects be populated with all data? Otherwise only main fields are filled
	 *
	 * @return Team[] array of teams
	 */
	public function getTeams($full=false)
	{
		if ($this->teams && $this->teamfulldata == $full) {
			return  $this->teams;
		}
		$leagues = $this->getLeagues($this->regularteamsonly, $this->allperiods);
		$this->teams = array();
		foreach($leagues as $teamcode=>$teamleagues) {
			$teamleague = reset($teamleagues);
			$this->teams[$teamcode] = $teamleague->getTeam($full); // extract team of first element
			$this->teams[$teamcode]->clubindex = $this->api->getClubsManager()->getClubIndex($this->teams[$teamcode]->api->getKey());
		}
		$this->teamfulldata = $full;
		return  $this->teams;
	}
	
	
	
	/**
	 * Get scheduled matches for the club
	 *
	 * @param integer $daysahead return only matches maximum x days ahead relative to $weekoffset (default: 30)
	 * @param integer $weekoffset return matches from week y relative to current week (0 = this week, 1 = next week, 2 = ...)  (default: 0) 
	 * @param integer $teamcode return only matches from team
	 * @param boolean $onlyownteam return only matches from own League
	 * @param string $sortorder (use options_sortorder)
	 * @param integer $rowcount max number of rows to return (default: 100) 
	 * @param boolean $home return home matches
	 * @param boolean $away return away matches
	 * @param string $matchtype (use options_matchtype)
	 * @param string $leaguetype (use options_leaguetype)
	 * @param string $daytype (use options_daytype)
	 * @param string $agecategory (use options_agecategory)
	 * @return ClubMatch[]
	 */
	public function getSchedule($daysahead=null, $weekoffset=null, $teamcode=null, $onlyownteam=false, $sortorder=null, $rowcount=null, $home=true, $away=true, $matchtype=null, 
			$leaguetype=null, $daytype=null, $agecategory=null)
	{
	    $params = array();
	    if (isset($daysahead)) $params['aantaldagen'] = $daysahead;
		if (isset($weekoffset)) $params['weekoffset'] = $weekoffset;
		if (isset($teamcode)) $params['teamcode'] = $teamcode;
		$params['eigenwedstrijden'] = $onlyownteam ? 'JA' : 'NEE';
		if (isset($sortorder)) $params['sorteervolgorde'] = $sortorder;
		$params['thuis'] = $home ? 'JA' : 'NEE';
		$params['uit'] = $away ? 'JA' : 'NEE';
		if (isset($matchtype)) $params['spelsoort'] = $matchtype;
		if (isset($leaguetype)) $params['competitiesoort'] = $leaguetype;
		if (isset($daytype)) $params['dagsoort'] = $daytype;
		if (isset($agecategory)) $params['leeftijdscategorie'] = $agecategory;
		if (isset($rowcount)) $params['aantalregels'] = $rowcount; 
		$params['gebruiklokaleteamgegevens'] = 'NEE';
		
		$response = $this->api->request('programma', $params);
	
		
		/** @var ClubMatch[] $matches */
		$matches = array();
		foreach($response as $item){
			/** @var ClubMatch $clubmatch */
			$clubmatch = $this->api->map($item, new ClubMatch($this->api));
			$matches[$clubmatch->wedstrijdcode] = $clubmatch;
		}
	
		return  $matches;
	}

	/**
	 * Get cancelled matches for the club
	 *
	 * @param integer $daysahead return only matches maximum x days ahead relative to $weekoffset (default: 30)
	 * @param integer $weekoffset return matches from week y relative to current week (0 = this week, 1 = next week, 2 = ...)  (default: 0)
	 * @param string $sortorder (use options_sortorder, default: datum)
	 * @param integer $rowcount max number of rows to return (default: 20)
	 * @return ClubMatchCancellation[]
	 */
	public function getCancellations($daysahead=null, $weekoffset=null, $sortorder=null, $rowcount=null)
	{
	    $params = array();
	    if (isset($daysahead)) $params['aantaldagen'] = $daysahead;
		if (isset($weekoffset)) $params['weekoffset'] = $weekoffset;
		if (isset($sortorder)) $params['sorteervolgorde'] = $sortorder;
		if (isset($rowcount)) $params['aantalregels'] = $rowcount;
		$params['gebruiklokaleteamgegevens'] = 'NEE';
	
		$response = $this->api->request('afgelastingen', $params);
	
	
		/** @var ClubMatchCancellation[] $matches */
		$matches = array();
		foreach($response as $item){
			/** @var ClubMatchCancellation $clubmatch */
			$clubmatch = $this->api->map($item, new ClubMatchCancellation($this->api));
			$matches[$clubmatch->wedstrijdcode] = $clubmatch;
		}
	
		return  $matches;
	}
	
	/**
	 * Get results of played matches of the club
	 *
	 * @param integer $daysahead return only matches maximum x days ahead relative to $weekoffset (default: 7)
	 * @param integer $weekoffset return matches from week y relative to current week (0 = this week, 1 = next week, 2 = ...)  (default: -1)
	 * @param integer $teamcode return only matches from team
	 * @param integer $localteamcode return only matches from localteam
	 * @param boolean $onlyownteam return only matches from own League
	 * @param string $sortorder (use options_sortorder)
	 * @param integer $rowcount max number of rows to return (default: 100)
	 * @param string $matchtype (use options_matchtype)
	 * @return ClubMatchResult[]
	 */
	public function getResults($daysahead=null, $weekoffset=null, $teamcode=null, $localteamcode=null, $onlyownteam=false, $sortorder=null, $rowcount=null, 
			$matchtype=null, $agecategory=null)
	{
	    $params = array();
	    if (isset($daysahead)) $params['aantaldagen'] = $daysahead;
		if (isset($weekoffset)) $params['weekoffset'] = $weekoffset;
		if (isset($teamcode)) $params['teamcode'] = $teamcode;
		if (isset($localteamcode)) $params['lokaleteamcode'] = $localteamcode;
		$params['eigenwedstrijden'] = $onlyownteam ? 'JA' : 'NEE';
		if (isset($sortorder)) $params['sorteervolgorde'] = $sortorder;
		if (isset($rowcount)) $params['aantalregels'] = $rowcount;
		if (isset($matchtype)) $params['spelsoort'] = $matchtype;
		if (isset($agecategory)) $params['leeftijdscategorie'] = $agecategory;
		$params['gebruiklokaleteamgegevens'] = 'NEE';
		
		$response = $this->api->request('uitslagen', $params);
	
		/** @var ClubMatchResult[] $matches */
		$matches = array();
		foreach($response as $item){
			// Convert "Ja"/"Nee" to true/false
			$item['verenigingswedstrijd'] = ($item['verenigingswedstrijd'] == "Ja");
			
			/** @var ClubMatchResult $clubmatch */
			$clubmatch = $this->api->map($item, new ClubMatchResult($this->api));
			$matches[$clubmatch->wedstrijdcode] = $clubmatch;
		}
	
		return  $matches;
	}
	
	/**
	 * Get anniversaries
	 *
	 * @param integer $daycount number of days ahead to return (default: 21)
	 * @return Anniversary[]
	 */
	public function getAnniversaries($daycount=null)
	{
		if ($this->anniversaries) {
			return $this->anniversaries;
		}
		
		$params=[];
		if (isset($daycount)) $params['aantaldagen'] = $daycount;
		$response = $this->api->request('verjaardagen', $params);
		
		$this->anniversaries = array();
		foreach($response as $item){
			$this->anniversaries[] = $this->api->map($item, new Anniversary($this->api));
		}
		
		return $this->anniversaries;
	}
	

	/**
	 * Get commissions
	 *
	 * @return Commission[]
	 */
	public function getCommissions()
	{
	    if ($this->commissions) {
	        return $this->commissions;
	    }

	    $params=[];
	    $response = $this->api->request('commissies', $params);
	    
	    $this->commissions = array();
	    foreach($response as $item){
	        /** @var Commission $commission */
	    	$commission = $this->api->map($item, new Commission($this->api));
	        $this->commissions[$commission->commissiecode] = $commission;
	    }
	    
	    return  $this->commissions;
	}
	
	/**
	 * Get commissions
	 *
	 * @return BoardMember[]
	 */
/*
    public function getBoardMembers()
	{
	    if ($this->board) {
	        return $this->board;
	    }
	    
	    $params=[];
	    $response = $this->api->request('bestuur', $params);
	    
	    $this->board = array();
	    foreach($response as $item){
	        // @var BoardMember $board 
	        $board = $this->api->map($item, new BoardMember($this));
	        $this->board[$board->xxxxxxx] = $board;
	    }
	    
	    return  $this->commissions;
	}
*/	
}
