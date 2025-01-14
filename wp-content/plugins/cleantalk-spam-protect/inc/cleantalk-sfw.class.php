<?php
class CleanTalkSFW
{
	public $ip = 0;
	public $ip_str = '';
	public $result = false;
	
	public function cleantalk_get_real_ip()
	{
		if ( function_exists( 'apache_request_headers' ) )
		{
			$headers = apache_request_headers();
		}
		else
		{
			$headers = $_SERVER;
		}
		if ( array_key_exists( 'X-Forwarded-For', $headers ) )
		{
			$the_ip=explode(",", trim($headers['X-Forwarded-For']));
			$the_ip = trim($the_ip[0]);
		}
		elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ))
		{
			$the_ip=explode(",", trim($headers['HTTP_X_FORWARDED_FOR']));
			$the_ip = trim($the_ip[0]);
		}
		else
		{
			$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		}
		if(isset($_GET['sfw_test_ip']))
		{
			$the_ip=$_GET['sfw_test_ip'];
		}
		$this->ip_str=$the_ip;
		$this->ip=sprintf("%u", ip2long($the_ip));
		//print sprintf("%u", ip2long($the_ip));
	}
	
	public function check_ip()
	{
		global $wpdb,$ct_options, $ct_data;
		//print "select network from `".$wpdb->base_prefix."cleantalk_sfw` where ".$this->ip." & mask = network;";
		$r = $wpdb->get_results("select network from `".$wpdb->base_prefix."cleantalk_sfw` where network = ".$this->ip." & mask;", ARRAY_A);
		
		if(isset($ct_data['sfw_log']))
		{
			$sfw_log=$ct_data['sfw_log'];
		}
		else
		{
			$sfw_log=array();
		}
		
		if(isset($sfw_log[$this->ip_str]))
		{
			$sfw_log[$this->ip_str]['all']++;
		}
		else
		{
			$sfw_log[$this->ip_str] = Array('all' => 1, 'block' => 0);
		}
		
		if(sizeof($r)>0)
		{
			$sfw_log[$this->ip_str]['block']++;
			$this->result=true;
		}
		else
		{
			setcookie ('ct_sfw_pass_key', md5($this->ip_str.$ct_options['apikey']), 0, "/");
		}
		$ct_data['sfw_log'] = $sfw_log;
		update_option('cleantalk_data', $ct_data);
		if($this->result)
		{
			@header("CtSFWCheck: block");
		}
		else
		{
			@header("CtSFWCheck: allow");
		}
	}
	
	public function sfw_die()
	{
		global $ct_options, $ct_data;
		$sfw_die_page=file_get_contents(dirname(__FILE__)."/sfw_die_page.html");
		$sfw_die_page=str_replace("{REMOTE_ADDRESS}",$this->ip_str,$sfw_die_page);
		$sfw_die_page=str_replace("{REQUEST_URI}",$_SERVER['REQUEST_URI'],$sfw_die_page);
		$sfw_die_page=str_replace("{SFW_COOKIE}",md5($this->ip_str.$ct_options['apikey']),$sfw_die_page);
		@header('HTTP/1.0 403 Forbidden');
		wp_die( $sfw_die_page, "Blacklisted", Array('response'=>403) );
	}
	
	public function send_logs()
	{
		global $ct_options, $ct_data;
		$ct_options = ct_get_options();
	    $ct_data = ct_get_data();
	    
	    if(isset($ct_options['spam_firewall']))
	    {
	    	$value = @intval($ct_options['spam_firewall']);
	    }
	    else
	    {
	    	$value=0;
	    }
	    
	    if($value==1 && isset($ct_data['sfw_log']))
	    {
	    	$sfw_log=$ct_data['sfw_log'];
	    	$data=Array();
	    	foreach($sfw_log as $key=>$value)
	    	{
	    		$data[]=Array($key, $value['all'], $value['block']);
	    	}
	    	$qdata = array (
				'data' => json_encode($data),
				'rows' => count($data),
				'timestamp' => time()
			);
			
			$result = sendRawRequest('https://api.cleantalk.org/?method_name=sfw_logs&auth_key='.$ct_options['apikey'],$qdata);
			$result = json_decode($result);
			if(isset($result->data) && isset($result->data->rows))
			{
				if($result->data->rows == count($data))
				{
					$ct_data['sfw_log']=Array();
					update_option('cleantalk_data', $ct_data);
				}
			}
			
	    }
	}
}