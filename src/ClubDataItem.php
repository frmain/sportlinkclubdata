<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class ClubDataItem
{
	/** @var ClubData */
	protected $api;

	/**
	 * @param ClubData $api
	 */
	public function __construct(ClubData $api)
	{
		$this->api = $api;
	}
	
	/**
	 * function to determine if name of a person is protected by user (GDPR)
	 * @param string value
	 * @return boolean
	 */
	protected function getPrivate($value) {
		return (stripos($value, "AFGESCHERMD")!==false); // there is no better way to do this unfortunately
	}
	
}