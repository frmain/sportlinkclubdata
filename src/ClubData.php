<?php
namespace SportlinkClubData;

use JsonMapper;
use SportlinkClubData\Exception\InvalidResponseException;
use SportlinkClubData\HttpClient\HttpClient;
use SportlinkClubData\HttpClient\HttpClientInterface;



/**
 * Sportlink Club.data top-level class
 *
 */
class ClubData
{

	/** @var HttpClientInterface $client  */
	protected $client;

	/** @var JsonMapper $mapper */
	protected $mapper;

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
	
	/** @var Option[] */
	protected $options_sortorder = [];
	
	/** @var Option[] */
	protected $options_leagueperiod = [];
	
	/** @var Option[] */
	protected $options_leaguetype = [];
	
	/** @var Option[] */
	protected $options_daytype = [];
	
	/** @var Option[] */
	protected $options_invoicestatus = [];
	
	/** @var Option[] */
	protected $options_sex = [];
	
	/** @var Option[] */
	protected $options_agecategory = [];
	
	/** @var Option[] */
	protected $options_matchtype = [];
	
	/** @var Option[] */
	protected $options_teamrole = [];
	
	/** @var Option[] */
	protected $options_teamtype = [];
	
	/** @var Commission[]  */
	protected $commissions = [];
	
    /**
	 * @param string $key
	 * @param HttpClientInterface $client
	 */
	public function __construct($key, HttpClientInterface $client = null)
	{
		$this->key = $key;
		$this->client = $client ?: new HttpClient();
		$this->mapper = new JsonMapper();
	}

	/**
	 * Make a request to the Sportlink API
	 *
	 * @param $path
	 * @param array $parameters
	 * @throws InvalidResponseException
	 * @return array
	 */
	public function request($path, $parameters = [])
	{
		try {
			$parameters['client_id'] = $this->key;
			$milliseconds = round(microtime(true) * 1000);
			
			$data = $this->client->get($path, $parameters);
			
			// logging
			$logmsg=(new \DateTime())->format('Y/m/d H:i:s') . ": HTTP GET in " . number_format(round(microtime(true) * 1000)-$milliseconds,0,'.','') . "ms on path: " . $path . ", with parameters: " . implode(',', array_map(function ($v, $k) { return $k.':'.$v; }, $parameters, array_keys($parameters))) . "\r\n";
			if (!defined('_JEXEC') || JDEBUG)
			    error_log($logmsg, 3, 'd:\sites\temp\sportlinkclubdata.log');
			
/*		}catch(\GuzzleHttp\Exception\ParseException $e){
			throw new InvalidResponseException('Cannot parse message: '.$e->getResponse()->getBody(), $e->getCode());
*/			
		}catch(\GuzzleHttp\Exception\RequestException $e) {
			if ($e->getCode() == 401) { 
				throw new InvalidResponseException('Authorisation error: no clientID, invalid or expired clientID provided. Request: '.$e->getRequest()->getUri(),
					$e->getCode());
			} else {	
				throw new InvalidResponseException('Cannot finish request: ' . $e->getMessage(). ', Request: '.$e->getRequest()->getUri(), 
					$e->getCode());
			}
		}catch(\Exception $e){
			throw new InvalidResponseException($e->getMessage(), $e->getCode());
		}

		return $data;
	}

	/**
	 * Map data to an object
	 *
	 * @param array $json
	 * @param mixed $object
	 * @throws InvalidResponseException
	 * @return mixed object
	 */
	public function map($json, $object)
	{
		try {
		    $res = $this->mapper->map((object) $json, $object);
		} catch (\JsonMapper_Exception $e) {
		    throw new InvalidResponseException('Field mapping error: ' . $e->getMessage(), 0);
		}
		return $res;
	}


	
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

		$this->club = new Club($this);
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
		$response = $this->request('teams', $params);
	
		$this->leagues = array();
		foreach($response as $item){
			/** @var League $league */
			$league = $this->map($item, new League($this));
			$this->leagues[$league->teamcode][$league->poulecode] = $league;
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
		
		$response = $this->request('programma', $params);
	
		
		/** @var ClubMatch[] $matches */
		$matches = array();
		foreach($response as $item){
			/** @var ClubMatch $match */
			$match = $this->map($item, new ClubMatch($this));
			$matches[$match->wedstrijdcode] = $match;
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
	
		$response = $this->request('afgelastingen', $params);
	
	
		/** @var ClubMatchCancellation[] $matches */
		$matches = array();
		foreach($response as $item){
			/** @var ClubMatchCancellation $match */
			$match = $this->map($item, new ClubMatchCancellation($this));
			$matches[$match->wedstrijdcode] = $match;
		}
	
		return  $matches;
	}
	
	/**
	 * Get results of played matches of the club
	 *
	 * @param integer $daysahead return only matches maximum x days ahead relative to $weekoffset (default: 7)
	 * @param integer $weekoffset return matches from week y relative to current week (0 = this week, 1 = next week, 2 = ...)  (default: -1)
	 * @param integer $teamcode return only matches from team
	 * @param integer $localteamcode return only matches from team
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
		if (isset($localteamcode)) $params['lokaleteamcode'] = $teamcode;
		$params['eigenwedstrijden'] = $onlyownteam ? 'JA' : 'NEE';
		if (isset($sortorder)) $params['sorteervolgorde'] = $sortorder;
		if (isset($rowcount)) $params['aantalregels'] = $rowcount;
		if (isset($matchtype)) $params['spelsoort'] = $matchtype;
		if (isset($agecategory)) $params['leeftijdscategorie'] = $agecategory;
		$params['gebruiklokaleteamgegevens'] = 'NEE';
		
		$response = $this->request('uitslagen', $params);
	
		/** @var ClubMatchResult[] $matches */
		$matches = array();
		foreach($response as $item){
			// Convert "Ja"/"Nee" to true/false
			$item['verenigingswedstrijd'] = ($item['verenigingswedstrijd'] == "Ja");
			
			/** @var ClubMatchResult $match */
			$match = $this->map($item, new ClubMatchResult($this));
			$matches[$match->wedstrijdcode] = $match;
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
		$response = $this->request('verjaardagen', $params);
		
		$this->anniversaries = array();
		foreach($response as $item){
			$this->anniversaries[] = $this->map($item, new Anniversary($this));
		}
		
		return $this->anniversaries;
	}
	
	


	/**
	 * Get optionlist
	 *
	 * @param string $listtype indicate optionlist
	 * @return Option[]
	 */
	protected function getOptions($listtype)
	{
		$response = $this->request('keuzelijst-' . $listtype);
	
		$options = array();
		foreach($response as $item){
			$options[] = $this->map($item, new Option($this));
		}
	
		return $options;
	}
	
	/**
	 * Get optionlist sortorder
	 *
	 * @return OptionSortorder[]
	 */
	public function getOptionsSortorder()
	{
		if ($this->options_sortorder) {
			return $this->options_sortorder;
		}
	
		$response = $this->request('keuzelijst-sorteervolgordes');
	
		$this->options_sortorder = array();
		foreach($response as $item){
			$this->options_sortorder[] = $this->map($item, new OptionSortorder($this));
		}
	
		return $this->options_sortorder;
	}
	
	/**
	 * Get optionlist leagueperiod
	 *
	 * @return Option[]
	 */
	public function getOptionsLeaguePeriod()
	{
		if ($this->options_leagueperiod) {
			return $this->options_leagueperiod;
		}
	
		$this->options_leagueperiod = $this->getOptions('competitieperiode');
		return $this->options_leagueperiod;
	}
	
	/**
	 * Get optionlist leaguetype
	 *
	 * @return Option[]
	 */
	public function getOptionsLeagueType()
	{
		if ($this->options_leaguetype) {
			return $this->options_leaguetype;
		}
	
		$this->options_leaguetype = $this->getOptions('competitiesoorten');
		return $this->options_leaguetype;
	}
	
	/**
	 * Get optionlist daytype
	 *
	 * @return Option[]
	 */
	public function getOptionsDayType()
	{
		if ($this->options_daytype) {
			return $this->options_daytype;
		}
	
		$this->options_daytype = $this->getOptions('dagsoorten');
		return $this->options_daytype;
	}
	
	/**
	 * Get optionlist invoicestatus
	 *
	 * @return Option[]
	 */
	public function getOptionsInvoiceStatus()
	{
		if ($this->options_invoicestatus) {
			return $this->options_invoicestatus;
		}
	
		$this->options_invoicestatus = $this->getOptions('factuurstatussen');
		return $this->options_invoicestatus;
	}
	
	/**
	 * Get optionlist sex
	 *
	 * @return Option[]
	 */
	public function getOptionsSex()
	{
		if ($this->options_sex) {
			return $this->options_sex;
		}
	
		$this->options_sex = $this->getOptions('geslacht');
		return $this->options_sex;
	}
	
	/**
	 * Get optionlist agecategory
	 *
	 * @return Option[]
	 */
	public function getOptionsAgeCategory()
	{
		if ($this->options_agecategory) {
			return $this->options_agecategory;
		}
	
		$this->options_agecategory = $this->getOptions('leeftijdscategorieen');
		return $this->options_agecategory;
	}
	
	/**
	 * Get optionlist matchtype
	 *
	 * @return Option[]
	 */
	public function getOptionsMatchType()
	{
		if ($this->options_matchtype) {
			return $this->options_matchtype;
		}
	
		$this->options_matchtype = $this->getOptions('spelsoorten');
		return $this->options_matchtype;
	}
	
	/**
	 * Get optionlist teamrole
	 *
	 * @return Option[]
	 */
	public function getOptionsTeamRole()
	{
		if ($this->options_teamrole) {
			return $this->options_teamrole;
		}
	
		$this->options_teamrole = $this->getOptions('teampersoonrollen');
		return $this->options_teamrole;
	}
	
	/**
	 * Get optionlist teamtype
	 *
	 * @return Option[]
	 */
	public function getOptionsTeamType()
	{
		if ($this->options_teamtype) {
			return $this->options_teamtype;
		}
	
		$this->options_teamtype = $this->getOptions('teamsoorten');
		return $this->options_teamtype;
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
	    $response = $this->request('commissies', $params);
	    
	    $this->commissions = array();
	    foreach($response as $item){
	        /** @var Commission $commission */
	        $commission = $this->map($item, new Commission($this));
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
	    $response = $this->request('bestuur', $params);
	    
	    $this->board = array();
	    foreach($response as $item){
	        // @var BoardMember $board 
	        $board = $this->map($item, new BoardMember($this));
	        $this->board[$board->xxxxxxx] = $board;
	    }
	    
	    return  $this->commissions;
	}
*/	
}
