<?php
namespace SportlinkClubData;

use JsonMapper;
use SportlinkClubData\Exception\InvalidResponseException;
use SportlinkClubData\HttpClient\HttpClient;
use SportlinkClubData\HttpClient\HttpClientInterface;


/**
 * @author Foeke
 *
 */
class DataManager
{
	/** @var HttpClientInterface $client  */
	protected $client;
	
	/** @var JsonMapper $mapper */
	protected $mapper;
	
	/** @var string $key	clientID sportlink  */
	protected $key;
	
	/** @var ClubsManager $clubsmanager	owner of the DataManager object */
	protected $clubsmanager;
	
	public function __construct(ClubsManager $owner, $key, HttpClientInterface $client = null)
	{
		$this->clubsmanager = $owner;
		$this->key = $key;
		$this->client = $client ?: new HttpClient();
		$this->mapper = new JsonMapper();
	}
	

	/**
	 * Make a request to the Sportlink API
	 *
	 * @param $path
	 * @param array $parameters
	 * @throws InvalidResponseException
	 * @return array
	 */
	public function request($path, $parameters = [], $key="")
	{
		try {
			$parameters['client_id'] = $key ?:$this->key;
			$milliseconds = round(microtime(true) * 1000);
			
			# retrieve the data
			$data = $this->client->get($path, $parameters);
			
			// logging
			$logmsg=(new \DateTime())->format('Y/m/d H:i:s') . ": HTTP GET in " . number_format(round(microtime(true) * 1000)-$milliseconds,0,'.','') . "ms on path: " . $path . ", with parameters: " . implode(',', array_map(function ($v, $k) { return $k.':'.$v; }, $parameters, array_keys($parameters))) . "\r\n";
			if (!defined('_JEXEC') || JDEBUG)
				error_log($logmsg, 3, 'd:\sites\temp\sportlinkclubdata.log');
				
				/*		}catch(\GuzzleHttp\Exception\ParseException $e){
				 throw new InvalidResponseException('Cannot parse message: '.$e->getResponse()->getBody(), $e->getCode());
				 */
		}catch(\GuzzleHttp\Exception\RequestException $e) {
			if ($e->getCode() == 401) {
				throw new InvalidResponseException('Authorisation error: no clientID, invalid or expired clientID provided. Request: '.$e->getRequest()->getUri(),
					$e->getCode());
			} else {
				throw new InvalidResponseException('Cannot finish request: ' . $e->getMessage(). ', Request: '.$e->getRequest()->getUri(),
					$e->getCode());
			}
		}catch(\Exception $e){
			throw new InvalidResponseException($e->getMessage(), $e->getCode());
		}
		
		return $data;
	}
	
	/**
	 * Map data to an object
	 *
	 * @param array $json
	 * @param mixed $object
	 * @throws InvalidResponseException
	 * @return mixed object
	 */
	public function map($json, $object)
	{
		try {
			$res = $this->mapper->map((object) $json, $object);
			# add the client id to each result
			$res->client_id = $this->key;
		} catch (\JsonMapper_Exception $e) {
			throw new InvalidResponseException('Field mapping error: ' . $e->getMessage(), 0);
		}
		return $res;
	}
	
	/**
	 * Get the client id key
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}
	
	/**
	 * Get the owner of this object
	 *
	 * @return ClubsManager
	 */
	public function getClubsManager()
	{
		return $this->clubsmanager;
	}
	
	
}