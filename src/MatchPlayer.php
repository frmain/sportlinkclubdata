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

	public function __construct(ClubData $api, $wedstrijdcode)
	{
		parent::__construct($api);
		$this->wedstrijdcode=$wedstrijdcode;
	}
	
	
}