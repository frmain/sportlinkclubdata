<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class Anniversary extends ClubDataItem
{
	/**
	 * @var \DateTime
	 */
	public $geboortedatum;

	/**
	 * @var string|null
	 */
	public $verjaardag;
	
	/**
	 * @var string|null
	 */
	public $volledigenaam;
	
	/**
	 * @var integer
	 */
	public $nieuweleeftijd;
	
	/**
	 * @var integer
	 */
	public $leeftijd;
	
}