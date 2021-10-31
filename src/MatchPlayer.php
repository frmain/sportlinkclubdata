<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class MatchPlayer extends Player
{

	/**
	 * @var integer
	 */
	private $wedstrijdcode;

	/**
	 * @param DataManager $api
	 * @param integer $wedstrijdcode
	 */
	public function __construct(DataManager $api, $wedstrijdcode)
	{
		parent::__construct($api);
		$this->wedstrijdcode=$wedstrijdcode;
	}
	
	
}