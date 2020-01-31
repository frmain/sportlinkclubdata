<?php
namespace SportlinkClubData;


/**
 * Class holding data of a match of the club  (programma)
 *
 */
class ClubMatch extends LeagueMatch
{

	/**
	 * @var string|null
	 */
	public $teamnaam;

	/**
	 * @var string|null
	 */
	public $teamvolgorde;
	
	/**
	 * @var string|null
	 */
	public $competitiesoort;
	
	/**
	 * @var string|null
	 */
	public $competitie;
	
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
	public $kaledatum;
	
	/**
	 * @var string|null
	 */
	public $vertrektijd;
	
	/**
	 * @var string|null
	 */
	public $status;
	
	/**
	 * @var string|null
	 */
	public $scheidsrechters;
	
	/**
	 * @var string|null
	 */
	public $scheidsrechter;
	
	/**
	 * @var string|null
	 */
	public $veld;
	
	/**
	 * @var string|null
	 */
	public $locatie;
	
	/**
	 * @var string|null
	 */
	public $rijders;
	
	/**
	 * @var string|null
	 */
	public $kleedkamerthuisteam;
	
	/**
	 * @var string|null
	 */
	public $kleedkameruitteam;
	
	/**
	 * @var string|null
	 */
	public $kleedkamerscheidsrechter;
	
	/**
	 * function to determine if name of scheidsrechter is protected by user
	 * @return boolean
	 */
	public function getRefereePrivate() {
		return $this->getPrivate($this->getReferee());
	}
	
	/**
	 * Get the referee out of the list of officials
	 * Sportlink offers 2 lists of officials, association officials (KNVB) and clubofficials based on these lists this function determines which person is (main) referee
	 * @return string|null
	 */
	public function getReferee()
	{
		// first check the match's scheidsrechter, second query the match details when there is no scheidsrechter stated in this match
		if ($this->scheidsrechter) {
			return $this->scheidsrechter;
		} else {
			return $this->getMatchDetail()->getReferee();
		}
	}
	
}