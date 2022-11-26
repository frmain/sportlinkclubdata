<?php
namespace SportlinkClubData;


/**
  * Class holding data of a match in a league  (poule-programma)
 *
 */
class LeagueMatch extends ClubDataItem
{
	
	/**
	 * @var integer
	 */
	public $wedstrijdcode;

	/**
	 * @var \DateTime
	 */
	public $wedstrijddatum;
	
	/**
	 * @var string
	 */
	public $datum;
	
	/**
	 * @var string
	 */
	public $aanvangstijd;
	
	/**
	 * @var string
	 */
	public $thuisteam;
	
	/**
	 * @var string
	 */
	public $uitteam;
	
	/**
	 * @var string|null
	 */
	public $thuisteamclubrelatiecode;
	
	/**
	 * @var string|null
	 */
	public $uitteamclubrelatiecode;
	
	/**
	 * @var string|null
	 */
	public $accommodatie;
	
	/**
	 * @var string|null
	 */
	public $plaats;
	
	/**
	 * @var string|null
	 */
	public $status;
	
	/**
	 * @var string|null
	 */
	public $wedstrijdnummer;
	
	/**
	 * @var string|null
	 */
	public $datumopgemaakt;
	
	/**
	 * @var string|null
	 */
	public $wedstrijd;
	
	/**
	 * @var string|null
	 */
	public $thuisteamid;
	
	/**
	 * @var string|null
	 */
	public $uitteamid;
	
	/**
	 * @var boolean
	 */
	public $eigenteam;
	
	/**
	 * @var string|null
	 */
	public $uitslag;
	
	/**
	 * @var MatchDetail
	 * 
	 * PHP8: changed $match to $amatch, as match is a reserved keyword
	 */
	protected $amatch;
	
	/**
	 * Get the match details of a league match
	 *
	 * @return MatchDetail match details
	 */
	
	public function getMatchDetail()
	{
		if ($this->amatch) {
			return $this->amatch;
		}
	
		$this->amatch = new MatchDetail($this->api, $this->wedstrijdcode);
		return $this->amatch;
	}
	
	/**
	 * Experimental: returns a code for the status of a match
	 *
	 * @return integer code 0 is normal, other codes means special condition
	 */
	public function getStatuscode() {
	    switch ($this->status) {
	        case "Te spelen": $val = 0; break;
	        case "Afgelast door bond": $val = 1; break;
	        case "Afgelast door vereniging": $val = 1; break;
	        // case :
	        default: $val = 0; break;
	    }
	    return $val;
	}

	public function isHomeMatch() {
		return (in_array($this->thuisteamclubrelatiecode, $this->api->getClubsManager()->getClubcodes()));
	}

	public function isAwayMatch() {
		return (in_array($this->uitteamclubrelatiecode, $this->api->getClubsManager()->getClubcodes()));
	}
	
}