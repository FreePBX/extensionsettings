<?php
namespace FreePBX\modules;
use BMO;
use FreePBX_Helpers;

class Extensionsettings extends FreePBX_Helpers implements BMO
{
	protected $FreePBX;
	protected $db;
	protected $astman;
	protected $extensions;
    
	public function __construct($freepbx = null)
	{
		if ($freepbx == null) 
		{
			throw new \RuntimeException('Not given a FreePBX Object');
		}

		$this->FreePBX 	  = $freepbx;
		$this->db 		  = $freepbx->Database;
		$this->astman 	  = $freepbx->astman;
		$this->extensions = $freepbx->Extensions;
	}


	public function install() {}

	public function uninstall() {}

	public function doConfigPageInit($page) { }
	

    public function ajaxRequest($command, &$setting)
	{
		// ** Allow remote consultation with Postman **
		// ********************************************
		// $setting['authenticate'] = false;
		// $setting['allowremote'] = true;
		// return true;
		// ********************************************
		switch($command)
		{
			case "list":
				return true;
			break;
		}
		return false;
	}

    public function ajaxHandler()
	{
		$command = $this->getReq("command", "");
		$data_return = false;

		switch ($command)
		{
			case 'list':
				$showAll 	 = $this->getReq("showall", '') == "yes" ? true : false;
				$data_return = $this->getListExtensionsInfo($showAll);
				break;
			
			default:
				$data_return = array("status" => false, "message" => _("Command not found!"), "command" => $command);
				break;
		}
		return $data_return;
	}

    /**
	 * This returns html to the main page
	 *
	 * @return string html
	 */
	public function showPage($page, $params = array())
	{
		$request = $_REQUEST;
		$data = array(
			"extensionsettings" => $this,
			'request'           => $request,
			'page' 	            => $page,
		);
		$data = array_merge($data, $params);

		switch ($page)
		{
			case 'main':
				$data_return = load_view(__DIR__ . '/views/page.main.php', $data);
				break;

			case 'grid':
				$data_return  = load_view(__DIR__ . '/views/view.grid.php', $data);
				break;

			default:
				$data_return = sprintf(_("Page Not Found (%s)!!!!"), $page);
		}
		return $data_return;
	}

	public function getListExtension($showAll = false)
	{
		$data_return = array();
		
		// Need FREEPBX-24155
		$full_list 	 = $this->extensions->checkUsage(true);
		
		foreach ($full_list as $key => $value)
		{
			$rawname = $this->getActiveModuleRawName($key);
			if ($rawname != 'core' && $showAll == false)
			{
				continue; // we just want core
			}
			$data_return[$key] = $value;
		}
		return $data_return;
	}

	public function getListExtensionsInfo($showAll = false)
	{
		$ampuser     = $this->getAstMan('user');
		$settings    = $this->getAstMan('settings');
		$data_return = array();

		foreach ($this->getListExtension($showAll) as $key => $value)
		{
			$rawName = $this->getActiveModuleRawName($key);
			if ($rawName == 'core')
			{
				$pretyName =  \modgettext::_('Extensions', $rawName);
			}
			else
			{
				$pretyName =  \modgettext::_($this->getActiveModuleName($key), $rawName);
			}

			foreach ($value as $exten => $item)
			{
				$description = explode(":", $item['description'], 2);
				$description = trim($description[1]);
				//Hack for PHP 5.3 negative number key issue
				//https://bugs.php.net/bug.php?id=51008
				preg_match('/display=(\d+)&/i', $item['edit_url'], $matches);
				$exten = !empty($matches[1]) ? $matches[1] : $exten;
				//end hack
	
				// Is VmX enabled, check only busy, if VmX is enabled, we have either "disabled", "enabled" or "blocked" in one of the states.
				if ( isset($ampuser['/AMPUSER/'.$exten.'/vmx/busy/state']))
				{
					$vmxstate = true;
					$vmxbusy  = false;
					switch($ampuser['/AMPUSER/'.$exten.'/vmx/busy/state'])
					{
						case 'blocked':     // We have one of the states, if it is "blocked", set proper icon
							$vmxstate = false;
							break;
	
						case 'enabled':     // Get the states of the VmX, we have either Busy or Unavailable enabled
							$vmxbusy = true;
							break;
					}
	
					$vmxunavail = (isset($ampuser['/AMPUSER/'.$exten.'/vmx/unavail/state']) && $ampuser['/AMPUSER/'.$exten.'/vmx/unavail/state'] == "enabled");
					
					// Do we have a VmX Busy/Unavail number for 0? If we have, then show it, otherwise display "Operator"
					if( isset($ampuser['/AMPUSER/'.$exten.'/vmx/busy/0/ext']))
					{
						$vmxzero = $ampuser['/AMPUSER/'.$exten.'/vmx/busy/0/ext'];
						$vmxoperator = false;
					}
					else
					{
						$vmxzero = _("Operator");
						$vmxoperator = true;
					}
	
					// Do we have a VmX Busy/Unavail for 1? We only need to check Busy, as the number is the same for busy and unavail
					$vmxone = isset($ampuser['/AMPUSER/'.$exten.'/vmx/busy/1/ext']) ? $ampuser['/AMPUSER/'.$exten.'/vmx/busy/1/ext'] : '';
					
					// Do we have a VmX Busy/Unavailable number for 2?
					$vmxtwo = isset($ampuser['/AMPUSER/'.$exten.'/vmx/busy/2/ext']) ? $ampuser['/AMPUSER/'.$exten.'/vmx/busy/2/ext'] : '';
				}
				else
				{
					$vmxstate    = false;
					$vmxbusy     = false;
					$vmxunavail  = false;
					$vmxoperator = false;
					$vmxzero     = $vmxone = $vmxtwo = "";
				};

				// Has the extension followme enabled?
				$fmstate = (isset($ampuser['/AMPUSER/'.$exten.'/followme/ddial']) && in_array($ampuser['/AMPUSER/'.$exten.'/followme/ddial'], array('DIRECT', 'EXTENSION')));
				
				// If follow-me is enabled, get the follow-me list
				// $fmlist = $fmstate ? str_replace("-", "<br>", $ampuser['/AMPUSER/'.$exten.'/followme/grplist']) : '';
				$fmlist = $fmstate && ! empty($ampuser['/AMPUSER/'.$exten.'/followme/grplist']) ? explode("-", $ampuser['/AMPUSER/'.$exten.'/followme/grplist']) : array();

				$cw  = isset($settings['cw']['/CW/'.$exten]) && $settings['cw']['/CW/'.$exten] == "ENABLED" ? true : false;
				$dnd = isset($settings['dnd']['/DND/'.$exten]) && $settings['dnd']['/DND/'.$exten] == "YES" ? true : false;
				$cf  = isset($settings['cf']['/CF/'.$exten])                                                ? $settings['cf']['/CF/'.$exten]   : '';
				$cfb = isset($settings['cfb']['/CFB/'.$exten])                                              ? $settings['cfb']['/CFB/'.$exten] : '';
				$cfu = isset($settings['cfu']['/CFU/'.$exten])                                              ? $settings['cfu']['/CFU/'.$exten] : '';
				
				$data_return[] = array(
					'key' 		  => $key,
					'name' 		  => $pretyName,
					'edit_url' 	  => $item['edit_url'],
					'exten'       => $exten,
					'description' => (empty($description) ? $exten : $description),
					'vmxstate'    => $vmxstate,
					'vmxbusy'     => $vmxbusy,
					'vmxunavail'  => $vmxunavail,
					'vmxoperator' => $vmxoperator,
					'vmxzero'     => $vmxzero,
					'vmxone'      => $vmxone,
					'vmxtwo'      => $vmxtwo,
					'fm' 		  => $fmstate,
					'fmlist' 	  => $fmlist,
					'cw'  		  => $cw,
					'dnd' 		  => $dnd,
					'cf'  		  => $cf,
					'cfb' 		  => $cfb,
					'cfu' 		  => $cfu,
				);
			}
		}
		return $data_return;
	}

	public function getActiveModule($module)
	{
		$active_modules = $this->FreePBX->Modules->getActiveModules();
		return isset($active_modules[$module]) ? $active_modules[$module] : array('rawname' => '', 'name' => '');
	}

	public function getActiveModuleRawName($module)
	{
		$data = $this->getActiveModule($module);
		return $data['rawname'];
	}

	public function getActiveModuleName($module)
	{
		$data = $this->getActiveModule($module);
		return $data['name'];
	}

	public function getAstMan($option)
	{
		$data_return = array();
		switch($option)
		{
			case 'settings':
				$data_return = array (
					'cw'    => $this->astman->database_show("CW"),        // Get all CW settings
					'cf'    => $this->astman->database_show("CF"),        // Get all CF settings
					'cfb'   => $this->astman->database_show("CFB"),       // Get all CFB settings
					'cfu'   => $this->astman->database_show("CFU"),       // Get all CFU settings
					'dnd'   => $this->astman->database_show("DND"),       // Get all DND settings
				);
				break;

			case 'user':
				$data_return = $this->astman->database_show("AMPUSER");
				break;

			default:
				$data_return = array(
					'user'		=> $this->getAstMan('user'),
					'settings' 	=> $this->getAstMan('settings'),
				);
		}
		return $data_return;
	}

}