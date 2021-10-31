<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class MatchStatistics extends ClubDataItem
{

	/**
	 * @var string
	 */
	public $wedstrijdcode;
	
	/**
	 * @var MatchTeamStatistics
	 */
	protected $thuisteam;

	/**
	 * @var MatchTeamStatistics
	 */
	protected $uitteam;

	/**
	 * @param DataManager $api
	 * @param integer $wedstrijdcode
	 */
	public function __construct(DataManager $api, $wedstrijdcode)
	{
		parent::__construct($api);
		$this->wedstrijdcode=$wedstrijdcode;
		$this->populate();
	}
	
	/**
	 * @return void
	 */
	protected function populate()
	{
	    $params = array();
	    $params['wedstrijdcode'] = $this->wedstrijdcode;
	
		$response = $this->api->request('wedstrijd-statistieken', $params);
	
		if (isset($response) && isset($response['thuisteam'])) {
			$this->thuisteam = $this->api->map($response['thuisteam'], new MatchTeamStatistics($this->api));
		}
		if (isset($response) && isset($response['uitteam'])) {
			$this->uitteam =$this->api->map($response['uitteam'], new MatchTeamStatistics($this->api));
		}
		
	}

	/**
	 *
	 * @return MatchTeamStatistics
	 */
	public function getHomeTeam()
	{
		return $this->thuisteam;
	}
	
	/**
	 *
	 * @return MatchTeamStatistics
	 */
	public function getAwayTeam()
	{
		return $this->uitteam;
	}
	
	
}