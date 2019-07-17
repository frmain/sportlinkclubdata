<?php
namespace SportlinkClubData;


/**
 * Class with MatchDetails (wedstrijd-informatie) 
 * 
 *
 */
class Match extends ClubDataItem
{
	/**
	 * @var integer
	 */
	public $wedstrijdnummer;

	/**
	 * @var integer
	 */
	public $wedstijdnummerintern;
	
	/**
	 * @var string|null
	 */
	public $veldnaam;
	
	/**
	 * @var string|null
	 */
	public $veldlocatie;
	
	/**
	 * @var \DateTime|null
	 */
	public $vertrektijd;
	
	/**
	 * @var string|null
	 */
	public $rijder;
	
	/**
	 * @var integer|null
	 */
	public $thuisscore;
	
	/**
	 * @var integer|null
	 */
	public $uitscore;
	
	/**
	 * @var string|null
	 */
	public $klasse;
	
	/**
	 * @var string|null
	 */
	public $wedstrijdtype;
	
	/**
	 * @var string|null
	 */
	public $competitietype;
	
	/**
	 * @var string|null
	 */
	public $categorie;
	
	/**
	 * @var \DateTime
	 */
	public $wedstrijddatetime;
	
	/**
	 * @var \DateTime
	 */
	public $wedstrijddatum;
	
	/**
	 * @var string
	 */
	public $wedstrijddatumopgemaakt;
	
	/**
	 * @var \DateTime
	 */
	public $aanvangstijd;
	
	/**
	 * @var string
	 */
	public $aanvangstijdopgemaakt;
	
	/**
	 * @var string
	 */
	public $duur;
	
	/**
	 * @var string
	 */
	public $speltype;
	
	/**
	 * @var string|null
	 */
	public $aanduiding;
	
	/**
	 * @var string|null
	 */
	public $poule;
	
	/**
	 * @var integer|null
	 */
	public $thuisteamid;
	
	/**
	 * @var string|null
	 */
	public $thuisteam;
	
	/**
	 * @var integer|null
	 */
	public $uitteamid;
	
	/**
	 * @var string|null
	 */
	public $uitteam;
	
	/**
	 * @var string|null
	 */
	public $opmerkingen;

	/**
	 * @var MatchOfficials
	 */
	protected $officials;
	
	/**
	 * @var MatchDressingrooms
	 */
	protected $dressingrooms;
	
	/**
	 * @var MatchFacilities
	 */
	protected $facilities;
	
	/**
	 * @var MatchClub
	 */
	protected $thuisteamclub;
	
	/**
	 * @var MatchClub
	 */
	protected $uitteamclub;
	
	/**
	 * @var MatchStatistics
	 */
	protected $teamstatistics;
	
	/**
	 * @var MatchPlayers
	 */
	protected $players;
	
	/**
	 * @var MatchPastResult[]
	 */
	protected $pastresults;
	
	/**
	 * @param ClubData $api
	 * @param integer $wedstrijdcode
	 */
	public function __construct(ClubData $api, $wedstrijdcode)
	{
		parent::__construct($api);
		$this->wedstrijdnummerintern=$wedstrijdcode;
		$this->populate();
	}
	
	
	/**
	 * @return void
	 */
	protected function populate()
	{
	    $params = array();
	    $params['wedstrijdcode'] = $this->wedstrijdnummerintern;
	
		$response = $this->api->request('wedstrijd-informatie', $params);
	
		if (isset($response) && isset($response['wedstrijdinformatie'])) {
			$this->api->map($response['wedstrijdinformatie'], $this);
		}
		if (isset($response) && isset($response['officials'])) {
			$this->officials = $this->api->map($response['officials'], new MatchOfficials($this->api, $this->wedstijdnummerintern));
		}
		if (isset($response) && isset($response['kleedkamers'])) {
			$this->dressingrooms = $this->api->map($response['kleedkamers'], new MatchDressingrooms($this->api));
		}
		if (isset($response) && isset($response['accommodatie'])) {
			$this->facilities = $this->api->map($response['accommodatie'], new MatchFacilities($this->api));
		}
		if (isset($response) && isset($response['uitteam'])) {
			$this->uitteamclub = $this->api->map($response['uitteam'], new MatchClub($this->api));
		}
		if (isset($response) && isset($response['thuisteam'])) {
			$this->thuisteamclub = $this->api->map($response['thuisteam'], new MatchClub($this->api));
		}
	}

	/**
	 *
	 * @return MatchDressingrooms
	 */
	public function getDressingrooms()
	{
		return $this->dressingrooms;
	}
	
	/**
	 *
	 * @return MatchFacilities
	 */
	public function getFacilities()
	{
		return $this->facilities;
	}
	
	/**
	 *
	 * @return MatchOfficials
	 */
	public function getOfficials()
	{
		return $this->officials;
	}
	
	/**
	 *
	 * @return MatchClub
	 */
	public function getHomeTeamClub()
	{
		return $this->thuisteamclub;
	}
	
	/**
	 *
	 * @return MatchClub
	 */
	public function getAwayTeamClub()
	{
		return $this->uitteamclub;
	}
	
	/**
	 * 
	 * @return MatchTeamStatistics
	 */
	public function getStatisticsHomeTeam()
	{
		if (!$this->teamstatistics) {
			$this->teamstatistics = new MatchStatistics($this->api, $this->wedstijdnummerintern);
		}
		return $this->teamstatistics->getHomeTeam();
	}
	
	/**
	 *
	 * @return MatchTeamStatistics
	 */
	public function getStatisticsAwayTeam()
	{
		if (!$this->teamstatistics) {
			$this->teamstatistics = new MatchStatistics($this->api, $this->wedstijdnummerintern);
		}
		return $this->teamstatistics->getAwayTeam();
	}

	/**
	 *
	 * @return MatchPlayer[]
	 */
	public function getPlayersHomeTeam($withphoto=false)
	{
		if (!$this->players) {
			$this->players = new MatchPlayers($this->api, $this->wedstijdnummerintern);
		}
		return $this->players->getPlayersHome($withphoto);
	}
	
	/**
	 *
	 * @return MatchPlayer[]
	 */
	public function getPlayersAwayTeam($withphoto=false)
	{
		if (!$this->players) {
			$this->players = new MatchPlayers($this->api, $this->wedstijdnummerintern);
		}
		return $this->players->getPlayersAway($withphoto);
	}

	/**
	 *
	 * @return MatchPastResult[]
	 */
	public function getPastResults()
	{
		// for efficiency, no need to request officials when already available
		if ($this->pastresults) {
			return $this->pastresults;
		}
		
		$params = array();
		$params['wedstrijdcode'] = $this->wedstijdnummerintern;
		$response = $this->api->request('wedstrijd-historische-resultaten', $params);
		
		$this->pastresults = array();
		foreach($response as $item){
			/** @var MatchPastResult $pastresult */
			$pastresult = $this->api->map($item, new MatchPastResult($this->api));
			$this->pastresults[] = $pastresult;
		}
		
		return  $this->pastresults;
	}
	
	
}