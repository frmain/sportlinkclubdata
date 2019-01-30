<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class Team extends ClubDataItem
{
	/**
	 * @var string
	 */
	public $teamcode;

	/**
	 * @var string
	 */
	public $lokaleteamcode;
	
	/**
	 * @var string
	 */
	public $teamnaam;
	
	/**
	 * @var string
	 */
	public $teamnaam_full;
	
	/**
	 * additional field
	 * 
	 * @var string|null
	 */
	public $begindatum;
	
	/**
	 * additional field
	 * 
	 * @var \DateTime|null
	 */
	public $begindatetime;
	
	/**
	 * additional field
	 * 
	 * @var string|null
	 */
	public $einddatum;
	
	/**
	 * additional field
	 * 
	 * @var \DateTime|null
	 */
	public $einddatetime;
	
	/**
	 * additional field
	 * 
	 * @var string|null
	 */
	public $competitie;
	
	/**
	 * @var string|null
	 */
	public $geslacht;
	
	/**
	 * additional field
	 * 
	 * @var string
	 */
	public $categorie;
	
	/**
	 * additional field
	 * 
	 * @var string|null
	 */
	public $shirtkleur;
	
	/**
	 * additional field
	 * 
	 * @var string|null
	 */
	public $broekkleur;
	
	/**
	 * additional field
	 * 
	 * @var string|null
	 */
	public $kousen;
	
	/**
	 * additional field
	 * 
	 * @var string|null
	 */
	public $omschrijving;
	
	/**
	 * additional field
	 * 
	 * @var string|null
	 */
	public $teamfoto;
	
	/**
	 * @var string|null
	 */
	public $teamsoort;
	
	/**
	 * @var string|null
	 */
	public $spelsoort;
	
	/**
	 * @var string|null
	 */
	public $speeldag;

	/**
	 * @var string|null
	 */
	public $leeftijdscategorie;
	
	/**
	 * @var League[] 
	 */
	protected $leagues;
	
	/** @var boolean */
	protected $regularteamsonly = false;
	
	/** @var boolean */
	protected $allperiods = true;
	
	/** @var boolean */
	protected $fulldata = false;
	
	/**
	 * @var TeamPlayer[]
	 */
	protected $players;
	
	/** @var boolean */
	protected $withphoto = false;
	
	/**
	 * @param ClubData $api
	 * @param integer $teamcode
	 * @param integer $localteamcode
	 */
	public function __construct(ClubData $api, $teamcode, $localteamcode=-1, $extradata=array(), $fulldata=false)
	{
		parent::__construct($api);
		$this->teamcode=$teamcode;
		$this->lokaleteamcode=$localteamcode;
		if (key_exists("teamnaam_full", $extradata)) $this->teamnaam_full=$extradata["teamnaam_full"];
		if (key_exists("teamsoort", $extradata)) $this->teamsoort=$extradata["teamsoort"];
		if (key_exists("spelsoort", $extradata)) $this->spelsoort=$extradata["spelsoort"];
		if (key_exists("speeldag", $extradata)) $this->speeldag=$extradata["speeldag"];
		if (key_exists("geslacht", $extradata)) $this->geslacht=$extradata["geslacht"];
		if (key_exists("leeftijdscategorie", $extradata)) $this->leeftijdscategorie=$extradata["leeftijdscategorie"];
		if ($fulldata)
		  $this->populate();
	}

	
	/**
	 * Populate the additional fields with data 
	 * 
	 * @return void
	 */
	public function populate()
	{
		$params['teamcode'] = $this->teamcode;
		$params['lokaleteamcode'] = $this->lokaleteamcode;
		
		$response = $this->api->request('team-gegevens', $params);
	
		if (isset($response) && isset($response["team"])) {
			$this->api->map($response['team'], $this);
		}
		$this->fulldata=true;
	}
	
	/**
	 * Are the additional fields filled?
	 *
	 * @return void
	 */
	public function isPopulated()
	{
	    return $this->fulldata;
	}
		
	
	/**
	 * Get the list of leagues in which the team is participating
	 *
	 * @param boolean  return only regular leagues
	 * @return League[] array of leagues, keyed by the *poulecode*
	 */
	public function getLeagues($regularonly=false, $allperiods=true)
	{
		// for efficiency, no need to request teams when already available
		if ($this->leagues && $this->regularteamsonly == $regularonly && $this->allperiods == $allperiods) {
			return $this->leagues;
		}
	
		$params['teamcode']=$this->teamcode;
		$params['lokaleteamcode']=$this->lokaleteamcode;
		$response = $this->api->request('teampoulelijst', $params);
	
		/**
		 * Make use of clubdata->getLeagues to get full league data
		 *
		 * @var League[][] $club_lgs 
		 */
		$club_lgs = $this->api->getLeagues($regularonly, $allperiods);
		
		$this->leagues = array();
		foreach($response as $item){
			/** @var League $league */
			$league = $this->api->map($item, new League($this->api));
			
			/* Now search in $lgs on entry with corresponding poulecode AND teamcode
			/* Be alert that poulecode can possibly be null! */
			if (isset($club_lgs[$league->teamcode][$league->poulecode])) {
			     $this->leagues[$league->poulecode] = $club_lgs[$league->teamcode][$league->poulecode];
			}
		}
		$this->regularteamsonly = $regularonly;
		$this->allperiods = $allperiods;
		
		return  $this->leagues;
	}
	
	/**
	 * Get the players of the team
	 * 
	 * @param boolean $withphoto if the the players' photo needs to be retrieved
	 * @param int $selecttype 0=all, 1=players only, 2=staff only
	 * @return TeamPlayer[]
	 */
	public function getPlayers($withphoto=false, $selecttype=0)
	{
		// for efficiency, no need to request officials when already available
		if ($this->players && $this->withphoto == $withphoto) {
			return $this->players;
		}
	
		$params['teamcode']=$this->teamcode;
		$params['lokaleteamcode']=$this->lokaleteamcode;
		if ($withphoto) $params['toonlidfoto'] = 'JA';
		switch ($selecttype) {
			case 1: $params['teampersoonrol'] = 'SPELER'; break;
			case 2: $params['teampersoonrol'] = 'NIET-SPELER'; break;
			default: ;
		}
		$response = $this->api->request('team-indeling', $params);
	
		$this->players = array();
		foreach($response as $item){
			/** @var TeamPlayer $player */
			$player = $this->api->map($item, new TeamPlayer($this->api));
			$player->private = (stripos($item['naam'], "AFGESCHERMD")!==false); // there is no better way to do this unfortunately
			$this->players[] = $player;
		}
	
		return  $this->players;
	}
	
}