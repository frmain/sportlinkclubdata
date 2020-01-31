<?php
namespace SportlinkClubData;


/**
 * This class contains the club officials; these are the officials not appointed by the Bond but by the club
 * 
 * @author Foeke
 *
 */
class MatchClubOfficials extends ClubDataItem
{

	/**
	 * @var string|null
	 */
	public $verenigingsscheidsrechtercode;
	
	/**
	 * @var string|null
	 */
	public $overigeofficialcode;
	
	/**
	 * @var string|null
	 */
	public $verenigingsscheidsrechter;
	
	
	/**
	 * @var string|null
	 */
	public $overigeofficial;

	/**
	 * @param ClubData $api
	 */
	public function __construct(ClubData $api)
	{
		parent::__construct($api);
	}
	
	/**
	 * function to determine if name of verenigingsscheidsrechter is protected by user
	 * @return boolean
	 */
	public function getRefereePrivate() {
		return $this->getPrivate($this->verenigingsscheidsrechter);
	}
	
	/**
	 * function to determine if name of overigeofficial is protected by user
	 * @return boolean
	 */
	public function getOtherOfficialPrivate() {
		return $this->getPrivate($this->overigeofficial);
	}
	
}