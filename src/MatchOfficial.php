<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class MatchOfficial extends ClubDataItem
{

	/**
	 * @var string|null
	 */
	public $officialnaam;
	
	/**
	 * @var string|null
	 */
	public $officialomschrijving;
	
	/**
	 * @var string|null
	 */
	public $relatiecode;
	
	/**
	 * function to determine if name of player is protected by user
	 * @return boolean
	 */
	public function getOfficialPrivate() {
		return $this->getPrivate($this->officialnaam);
	}
	
}