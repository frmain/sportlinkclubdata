<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class MatchPlayers extends ClubDataItem
{

	/**
	 * @var integer
	 */
	private $wedstrijdcode;
	
	
	/**
	 * @var MatchPlayer[]
	 */
	protected $playershome;
	
	/**
	 * @var MatchPlayer[]
	 */
	protected $playersaway;
	
	
	/**
	 * @param ClubData $api
	 * @param integer $wedstrijdcode
	 */
	public function __construct(ClubData $api, $wedstrijdcode)
	{
		parent::__construct($api);
		$this->wedstrijdcode=$wedstrijdcode;
	}
	
	
	/**
	 * Get the list of players hometeam
	 *
	 * @return MatchPlayer[]
	 */
	public function getPlayersHome($withphoto=false)
	{
		// for efficiency, no need to request officials when already available
		if ($this->playershome) {
			return $this->playershome;
		}
	
		$params = array();
		$params['wedstrijdcode'] = $this->wedstrijdcode;
		if ($withphoto) $params['toonlidfoto'] = 'JA';
		
		$response = $this->api->request('wedstrijd-thuisteam', $params);
	
		$this->playershome = array();
		foreach($response as $item){
			/** @var MatchPlayer $player */
			$player = $this->api->map($item, new MatchPlayer($this->api, $this->wedstrijdcode));
			$this->playershome[] = $player;
		}
	
		return  $this->playershome;
	}
		
	/**
	 * Get the list of players awayteam
	 *
	 * @return MatchPlayer[]
	 */
	public function getPlayersAway($withphoto=false)
	{
		// for efficiency, no need to request officials when already available
		if ($this->playersaway) {
			return $this->playersaway;
		}
	
		$params = array();
		$params['wedstrijdcode'] = $this->wedstrijdcode;
		if ($withphoto) $params['toonlidfoto'] = 'JA';
		
		$response = $this->api->request('wedstrijd-uitteam', $params);
	
		$this->playersaway = array();
		foreach($response as $item){
			/** @var MatchPlayer $player */
			$player = $this->api->map($item, new MatchPlayer($this->api, $this->wedstrijdcode));
			$this->playersaway[] = $player;
		}
	
		return  $this->playersaway;
	}
	
}