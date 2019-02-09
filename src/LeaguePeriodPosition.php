<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class LeaguePeriodPosition extends ClubDataItem
{
	/**
	 * @var integer
	 */
	public $positie;

	/**
	 * @var integer
	 */
	public $teamcode;
	
	/**
	 * @var integer
	 */
	public $poulecode;
	
	/**
	 * @var string
	 */
	public $clubcode;
	
	/**
	 * @var string
	 */
	public $clubnaam;
	
	/**
	 * @var string
	 */
	public $teamnaam;

	/**
	 * @var integer
	 */
	public $aantalwedstrijden;

	/**
	 * @var integer
	 */
	public $gewonnen;

	/**
	 * @var integer
	 */
	public $gelijkspel;

	/**
	 * @var integer
	 */
	public $verloren;

	/**
	 * @var integer
	 */
	public $doelpuntenvoor;

	/**
	 * @var integer
	 */
	public $tegendoelpunten;

	/**
	 * @var integer
	 */
	public $doelsaldo;

	/**
	 * @var integer
	 */
	public $verliespunten;

	/**
	 * @var integer
	 */
	public $totaalpunten;

	/**
	 * @var boolean
	 */
	public $eigenteam;
		
}