<?php
namespace SportlinkClubData;


/**
 * Class with MatchDetails (wedstrijd-informatie) 
 * 
 *
 */
class MatchDetail extends ClubDataItem
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
	 * @var MatchClubOfficials
	 */
	protected $clubofficials;
	
	/**
	 * @var MatchOfficial[]
	 */
	protected $matchofficials;
	
	/**
	 * @var string|null
	 */
	protected $mainreferee;
	
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
	 * @param DataManager $api
	 * @param integer $wedstrijdcode
	 */
	public function __construct(DataManager $api, $wedstrijdcode)
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
			$this->clubofficials = $this->api->map($response['officials'], new MatchClubOfficials($this->api));
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
	 * @return MatchClubOfficials
	 */
	public function getClubOfficials()
	{
		return $this->clubofficials;
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
		// for efficiency, no need to request PastResults when already available
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
	
	/**
	 * Get the list of match officials, these are the officials appointed by the Bond
	 *
	 * @return MatchOfficial[]
	 */
	public function getMatchOfficials()
	{
		// for efficiency, no need to request officials when already available
		if ($this->matchofficials) {
			return $this->matchofficials;
		}
		
		$params = array();
		$params['wedstrijdcode'] = $this->wedstijdnummerintern;
		$response = $this->api->request('wedstrijd-officials', $params);
		
		$this->matchofficials = array();
		foreach($response as $item){
			/** @var MatchOfficial $official */
			$official = $this->api->map($item, new MatchOfficial($this->api));
			$this->matchofficials[] = $official;
		}
		
		return  $this->matchofficials;
	}
	
	/**
	 * Get the referee out of the list of officials
	 * Sportlink offers 2 lists of officials, based on these lists this function determines which person is (main) referee
	 * @return string|null
	 */
	public function getReferee()
	{
		// for efficiency, no need to request when already available
		if ($this->mainreferee) {
			return $this->mainreferee;
		}

		// MatchOfficials are assigned to the match by the association
		$ref_funcs_filter = array("Scheidsrechter", "Spelbegeleider", "Clubscheidsrechter");
		foreach ($this->getMatchOfficials() as $official) {
			if (in_array($official->officialomschrijving, $ref_funcs_filter)) {
				$this->mainreferee = $official->officialnaam;
				return $this->mainreferee;
			}
		}
		
		// ClubOfficials are assigned to the match by the club
		$this->mainreferee = $this->getClubOfficials()->verenigingsscheidsrechter;
		return  $this->mainreferee;
	}

	/**
	 * Is the persons name made private (GDPR)? 
	 * @return boolean
	 */
	public function getRefereePrivate() {
		return $this->getPrivate($this->mainreferee);
	}
	
}