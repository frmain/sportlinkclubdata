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
	
}