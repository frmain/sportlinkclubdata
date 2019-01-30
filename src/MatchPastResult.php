<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class MatchPastResult extends ClubDataItem
{

	/**
	 * @var integer
	 */
	private $wedstrijdcode;
	
	/**
	 * @var string
	 */
	public $seizoen;
	
	/**
	 * @var string
	 */
	public $wedstrijd;
	
	/**
	 * @var string
	 */
	public $datum;
	
	/**
	 * @var string
	 */
	public $wedstrijddatum;
	
	/**
	 * @var \DateTime
	 */
	public $wedstrijddatetime;
	
	/**
	 * @var string
	 */
	public $uitslag;
		
}