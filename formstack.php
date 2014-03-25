<?php
/**
 * Method calls to the Formstack API
 *
 * @package    Formstack Mapabout
 * @author     Soon Van - randomecho.com
 * @copyright  2014 Soon Van
 * @license    http://opensource.org/licenses/BSD-3-Clause
 */

class Formstack {

	private $api = 'https://www.formstack.com/api/v2/';

	/**
	 * Connect to the Formstack API
	 *
	 * @param   string   method and/or endpoint
	 * @return  mixed    response from API with details, or false
	 */
	public function connect($method)
	{
		global $formstack_token;
		$endpoint = $this->api.$method.'?oauth_token='.$formstack_token;

		try
		{
			$remote_hook = curl_init();
			curl_setopt($remote_hook, CURLOPT_URL, $endpoint);
			curl_setopt($remote_hook, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($remote_hook, CURLOPT_FAILONERROR, 0);
			curl_setopt($remote_hook, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($remote_hook, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($remote_hook, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($remote_hook, CURLOPT_TIMEOUT, 50000);
			$response = curl_exec($remote_hook);
			curl_close($remote_hook);

			if ( ! is_null($response))
			{
				$response = json_decode($response);
			}
			else
			{
				$response = false;
			}

			return $response;
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
			return false;
		}
	}

	/**
	 * Get forms under account, or details of a specific form
	 *
	 * @param   integer  form id
	 * @return  mixed
	 */
	public function get_form($formstack_form_id = '')
	{
		$method = 'form/';

		if ($formstack_form_id != '')
		{
			$method .= $formstack_form_id.'.json';
		}

		return $this->connect($method);
	}

	/**
	 * Get details of a form
	 *
	 * @param   integer  form id
	 * @return  mixed
	 */
	public function get_submissions($formstack_form_id)
	{
		$method = 'form/'.$formstack_form_id.'/submission.json';

		return $this->connect($method);
	}

	/**
	 * Get details of a form submission
	 *
	 * @param   integer  submission id
	 * @return  mixed
	 */
	public function get_details($submission_id)
	{
		$method = 'submission/'.$submission_id.'.json';

		return $this->connect($method);
	}

	/**
	 * Get name and address info from a form submission
	 *
	 * @param   integer  form id
	 * @return  mixed
	 */
	public function get_addresses($form_id)
	{
		$result = $this->get_submissions($form_id);
		$address_found = false;

		if (isset($result->total))
		{
			foreach ($result->submissions as $info)
			{
				$fullname = $address = '';
				$form_data = $this->get_details($info->id);
				$form_fields = $form_data->data;

				foreach ($form_fields as $captured_data)
				{
					// Sniff for field that captured the name data
					if (stripos($captured_data->value, 'first = ') !== false)
					{
						$fullname = $this->get_fullname($captured_data->value);
					}
					elseif (stripos($captured_data->value, 'address = ') !== false)
					{
						// Sniff for fields that captured address data
						$address = $this->get_line_address($captured_data->value);
						$address_found = true;
					}
				}

				if (trim($fullname) != '' && trim($address) != '' )
				{
					$addresses[] = array('fullname' => $fullname,
						'address' => $address,
					);
				}
			}

			if ($address_found && count($addresses) > 0)
			{
				return $addresses;
			}
		}

		return false;
	}

	/**
	 * Extract a name from form data and return as single full name
	 *
	 * @param   array    name from submission
	 * @return  string
	 */
	public function get_fullname($submitted_name)
	{
		$customer_name = explode("\n", $submitted_name);
		$first_name = trim(substr($customer_name[0], strpos($customer_name[0], '=') + 1));
		$last_name = trim(substr($customer_name[1], strpos($customer_name[1], '=') + 1));

		$fullname = $first_name.' '.$last_name;

		return $fullname;
	}

	/**
	 * Extracts address and returns as a single line
	 *
	 * @param   array    address from submission
	 * @return  string
	 */
	public function get_line_address($submitted_address)
	{
		$raw_address_part = explode("\n", $submitted_address);

		foreach ($raw_address_part as $address_line)
		{
			$address[] = trim(substr($address_line, strpos($address_line, '=') + 1));
		}

		$address = implode($address, ' ');

		return $address;
	}

}