<?php
namespace SportlinkClubData;


/**
 * League wrapper
 * 
 * @author Foeke
 *
 */
class League extends ClubDataItem
{
	/**
	 * @var integer
	 */
	public $teamcode;

	/**
	 * @var integer
	 */
	public $lokaleteamcode;
	
	/**
	 * @var integer|null
	 */
	public $poulecode;
	
	/**
	 * @var string|null
	 */
	public $teamnaam;
	
	/**
	 * @var string|null
	 */
	public $competitienaam;
	
	/**
	 * @var string|null
	 */
	public $klasse;
	
	/**
	 * @var string|null
	 */
	public $poule;
	
	
	/**
	 * @var string|null
	 */
	public $klassepoule;
	
	/**
	 * @var string|null
	 */
	public $spelsoort;
	
	/**
	 * @var string|null
	 */
	public $competitiesoort;
	
	/**
	 * @var string|null
	 */
	public $geslacht;
	
	/**
	 * @var string|null
	 */
	public $teamsoort;
	
	/**
	 * @var string|null
	 */
	public $leeftijdscategorie;
	
	/**
	 * @var string|null
	 */
	public $kalespelsoort;
	
	/**
	 * @var string|null
	 */
	public $speeldag;
	
	/**
	 * @var string|null
	 */
	public $speeldagteam;
	
	/**
	 * @var Team
	 */
	protected $team;
	
	/**
	 * @var LeaguePosition[]
	 */
	protected $ranking;

	/** @var LeaguePeriod[] */
	protected $options_period = [];
	
	/** @var boolean */
	protected $teamfulldata = false;
	
	/**
	 * If this class is instantiated with a $leagueid, then other member variables than $leagueid are not filled
	 * Sportlink does not provide a function to populate one league  
 	 * 
	 * @param DataManager $api
	 * @param integer $leagueid
	 */
	public function __construct(DataManager $api, $leagueid=null)
	{
		$this->api = $api;
		$this->poulecode = $leagueid;
	}
	
	/**
	 * Get the club team from the league
	 * 
	 * @param boolean $fulldata return Team object with only basic information (do not query for detailed data; better performance)
	 * 
	 * @return Team
	 */ 
	public function getTeam($fulldata=false)
	{
		if ($this->team && $this->teamfulldata == $fulldata) {
			return $this->team;
		}
		
		$data = array();
		$data["teamnaam_full"]= $this->teamnaam;
		$data["teamsoort"]= $this->teamsoort;
		$data["spelsoort"]= $this->spelsoort;
		$data["speeldag"]= $this->speeldag;
		$data["geslacht"]= $this->geslacht;
		$data["leeftijdscategorie"]= $this->leeftijdscategorie;
		
		$this->team = new Team($this->api, $this->teamcode, $this->lokaleteamcode, $data, $fulldata);
		$this->teamfulldata = $fulldata;
		return $this->team;
	}

	/**
	 * @param boolean $onlyownteam return only matches from own League
	 * @param integer $daysahead return only matches maximum x days ahead relative to $weekoffset (default: 30)
	 * @param integer $weekoffset return matches from week y relative to current week (0 = this week, 1 = next week, 2 = ...)  (default: 0) 
	 * 
	 * @return LeagueMatch[]
	 */
	public function getMatchSchedule($onlyownteam=false, $daysahead=null, $weekoffset=null)
	{
	    $params = array();
	    $params['poulecode'] = $this->poulecode;
		($onlyownteam) ? $params['eigenwedstrijden'] = 'JA' : $params['eigenwedstrijden'] = 'NEE';
		if (isset($daysahead)) $params['aantaldagen'] = $daysahead; 
		if (isset($weekoffset)) $params['weekoffset'] = $weekoffset;
		
		$response = $this->api->request('poule-programma', $params);
		
		$matches = array();
		foreach($response as $item){
			$item['eigenteam'] = ($item['eigenteam'] == "true");
			/** @var LeagueMatch $leaguematch */
			$leaguematch = $this->api->map($item, new LeagueMatch($this->api));
			$matches[$leaguematch->wedstrijdcode] = $leaguematch;
		}
		
		return  $matches;
	}
	
	/**
	 * @param boolean $onlyownteam return only matches from own League
	 * @param integer $daysahead return only matches maximum x days ahead relative to $weekoffset(default: 14)
	 * @param integer $weekoffset return matches from week y relative to current week (0 = this week, -1 = last week, -2 = 2 weeks ago, -3=...)  (default: -2)
	 *
	 * @return LeagueMatch[]
	 */
	public function getMatchResults($onlyownteam=false, $daysahead=null, $weekoffset=null)
	{
	    $params = array();
	    $params['poulecode'] = $this->poulecode;
		($onlyownteam) ? $params['eigenwedstrijden'] = 'JA' : $params['eigenwedstrijden'] = 'NEE';
		if (isset($daysahead)) $params['aantaldagen'] = $daysahead;
		if (isset($weekoffset)) $params['weekoffset'] = $weekoffset;
	
		$response = $this->api->request('pouleuitslagen', $params);
	
		$matches = array();
		foreach($response as $item){
			$item['eigenteam'] = ($item['eigenteam'] == "true");
			/** @var LeagueMatch $leaguematch */
			$leaguematch = $this->api->map($item, new LeagueMatch($this->api));
			$matches[$leaguematch->wedstrijdcode] = $leaguematch;
		}
	
		return  $matches;
	}

	/**
	 * @return LeaguePosition[]
	 */
	public function getRanking()
	{
		// for efficiency, no need to request officials when already available
		if ($this->ranking) {
			return $this->ranking;
		}
		
		$params = array();
		$params['poulecode'] = $this->poulecode;
		$response = $this->api->request('poulestand', $params);
		
		$this->ranking = array();
		foreach($response as $item){
			$item['eigenteam'] = ($item['eigenteam'] == "true");
			/** @var LeaguePosition $position */
			$position = $this->api->map($item, new LeaguePosition($this->api));
			$this->ranking[$position->positie] = $position;
		}
		
		return  $this->ranking;
	}
	
	/**
	 * Get optionlist period
	 *
	 * @return LeaguePeriod[]
	 */
	public function getPeriods()
	{
		if ($this->options_period) {
			return $this->options_period;
		}
		
		$params = array();
		$params['poulecode'] = $this->poulecode;
		$response = $this->api->request('keuzelijst-periodenummers', $params);
		
		$this->options_period = array();
		foreach($response as $item){
			// Convert "Ja"/"Nee" to true/false
			$item['huidig'] = ($item['huidig'] == "Ja");
			$item['poulecode'] = $this->poulecode;
				
			if (isset($item['waarde'])) {
			    $this->options_period[] = $this->api->map($item, new LeaguePeriod($this->api));
			}
		}
		
		return $this->options_period;
	}
	
	
}