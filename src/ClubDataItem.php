<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class ClubDataItem
{
	/** @var DataManager */
	protected $api;


	/**
	 * @param DataManager $api   sportlink api
	 */
	public function __construct(DataManager $api)
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