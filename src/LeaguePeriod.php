<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class LeaguePeriod extends ClubDataItem
{
	/**
	 * @var integer
	 */
	public $poulecode;
	
	/**
	 * @var integer|null
	 */
	public $waarde;
	
	/**
	 * @var string|null
	 */
	public $omschrijving;
	
	/**
	 * @var boolean
	 */
	public $huidig;
	
	/**
	 * @var LeaguePeriodPosition[]
	 */
	protected $ranking;
	
	/**
	 * @return LeaguePeriodPosition[]
	 */
	public function getRanking()
	{
		if ($this->ranking) {
			return $this->ranking;
		}
	
        $params = array();
		$params['poulecode'] = $this->poulecode;
		$params['periodenummer'] = $this->waarde;
		$response = $this->api->request('periodestand', $params);
	
		$this->ranking = array();
		foreach($response as $item){
			/** @var LeaguePeriodPosition $position */
			$item['eigenteam'] = ($item['eigenteam'] == "true");
			$position = $this->api->map($item, new LeaguePeriodPosition($this->api));
			$this->ranking[$position->positie] = $position;
		}
	
		return  $this->ranking;
	}
	
	
}