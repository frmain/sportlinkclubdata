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
	
	
}