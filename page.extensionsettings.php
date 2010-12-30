<?php /* $Id */
// Copyright (C) 2008 Philippe Lindheimer & Bandwidth.com (plindheimer at bandwidth dot com)
// Copyright (C) 2010 Mikael Carlsson (mickecarlsson at gmail dot com)
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation, version 2
// of the License.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// TODO:
// Localization
// get all settings from astdb for extensions
// make all toggles clickable so they can be changed here
// like, click on CW ON or OFF to toggle state, perhaps an image?
// same for VmX, enable or disable, or if time permits, change destinations, who knows, time is the limit
// Change the table so it has colors, perhaps make it scrollable with the headers intact??
// and a dozen other things that I can think of.
//

$dispnum = 'extensionsettings';
$extension = _("Extension");
$vmxlocator = _("VmX Locator");
$followme   = _("Follow-Me");
$callstatus = _("Call status");
$status     =_("Status");

global $active_modules;

$html_txt = '<div class="content">';

if (!$extdisplay) {
	$html_txt .= '<br><h2>'._("FreePBX Extension Settings").'</h2>';
}
$full_list = framework_check_extension_usage(true);
// Dont waste astman calls, get all family keys in one call
// Get all AMPUSER settings
$ampuser = $astman->database_show("AMPUSER");
// Get all CW settings
$cwsetting = $astman->database_show("CW");
// get all CF settings
$cfsetting = $astman->database_show("CF");
// get all CFB settings
$cfbsetting = $astman->database_show("CFB");
// get all CFU settings
$cfusetting = $astman->database_show("CFU");

foreach ($full_list as $key => $value) {
	$vmxcolor = "\"BLACK\"";

	$sub_heading_id = $txtdom = $active_modules[$key]['rawname'];
	if ($active_modules[$key]['rawname'] != 'core' || ($quietmode && !isset($_REQUEST[$sub_heading_id]))) {
		continue; // we just want core
	}
	if ($txtdom == 'core') {
		$txtdom = 'amp';
		$active_modules[$key]['name'] = 'Extensions';
		$core_heading = $sub_heading =  dgettext($txtdom,$active_modules[$key]['name']);
	} else {
		$sub_heading =  dgettext($txtdom,$active_modules[$key]['name']);
	}
	$module_select[$sub_heading_id] = $sub_heading;
	$html_txt_arr[$sub_heading] =   "<div class=\"$sub_heading_id\"><table id=\"set_table\" border=\"0\" width=\"85%\"><tr>";
	$html_txt_arr[$sub_heading] .=  "<tr><td><strong>".$extension."</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td colspan=\"7\" align=\"center\"><strong>".$vmxlocator."</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td colspan=\"2\" align=\"center\"><strong>".$followme."</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td colspan=\"4\" align=\"center\"><strong>".$callstatus."</strong></td>";
	$html_txt_arr[$sub_heading] .=  "</tr><td></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>".$status."</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>Busy</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>Unavail</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>Operator</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>Press 0</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>Press 1</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>Press 2</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>FM</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>FM-list</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>CW</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>CF</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>CFB</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>CFU</strong></td></tr>\n";

	foreach ($value as $exten => $item) {
		$vmxzero = "";
		$vmxbusy = "<img src=\"images/cancel.png\" alt=\"Off\"/>";
		$vmxunavail = "<img src=\"images/cancel.png\" alt=\"Off\"/>";
		$description = explode(":",$item['description'],2);	
		$html_txt_arr[$sub_heading] .= "<tr><td><a href=\"".$item['edit_url']."\" class=\"info\">".$exten."<span>".(trim($description[1])==''?$exten:$description[1])."</span></a></td>";
		// Is VmX enabled, check only busy, if VmX is enabled, we have either "disabled", "enabled" or "blocked" in one of the states.
		if ( isset($ampuser['/AMPUSER/'.$exten.'/vmx/busy/state'])) {
		    // We have one of the states, if it is "blocked", set proper icon
		    if ($ampuser['/AMPUSER/'.$exten.'/vmx/busy/state'] == "blocked" ) {
			$vmxstate = "<img src=\"images/cancel.png\" alt=\"Off\"/>";
			$vmxcolor = "\"GREY\"";
		    } else {
			$vmxstate = "<img src=\"images/accept.png\" alt=\"On\"/>";
		    };
		    // Get the states of the VmX, we have either Busy or Unavailable enabled
		    if ($ampuser['/AMPUSER/'.$exten.'/vmx/busy/state'] == "enabled") {
			$vmxbusy = "<img src=\"images/accept.png\" alt=\"On\"/>";
		    } else {
			$vmxbusy = "<img src=\"images/cancel.png\" alt=\"Off\"/>";
		    };
		    if ($ampuser['/AMPUSER/'.$exten.'/vmx/unavail/state'] == "enabled") {
			$vmxunavail = "<img src=\"images/accept.png\" alt=\"On\"/>";
		    } else {
		    };
		    // Do we have a VmX Busy/Unavail number for 0? If we have, then show it, otherwise display "Operator"
		    if( isset($ampuser['/AMPUSER/'.$exten.'/vmx/busy/0/ext'])) {
		        $vmxzero = $ampuser['/AMPUSER/'.$exten.'/vmx/busy/0/ext'];
		        $vmxoperator = "<img src=\"images/cancel.png\" alt=\"Off\"/>";
		    } else {
		    	 $vmxzero = "Operator";
		    	 $vmxoperator = "<img src=\"images/accept.png\" alt=\"On\"/>";
		    }
		    // Do we have a VmX Busy/Unavail for 1? We only need to check Busy, as the number is the same for busy and unavail
		    if( isset($ampuser['/AMPUSER/'.$exten.'/vmx/busy/1/ext'])) {
		        $vmxone = $ampuser['/AMPUSER/'.$exten.'/vmx/busy/1/ext'];
		    } else {
		    	 $vmxone = "";
		    }
		    // Do we have a VmX Busy/Unavailable number for 2?
		    if( isset($ampuser['/AMPUSER/'.$exten.'/vmx/busy/2/ext'])) {
		        $vmxtwo = $ampuser['/AMPUSER/'.$exten.'/vmx/busy/2/ext'];
		    } else {
		    	$vmxtwo = "";
		    }
		 } else { 
		    $vmxstate = "<img src=\"images/cancel.png\" alt=\"Off\"/>";
		    $vmxone = $vmxtwo = "";
		    };
		
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$vmxstate."</td>";
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$vmxbusy."</td>";
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$vmxunavail."</td>";
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$vmxoperator."</td>";
		$html_txt_arr[$sub_heading] .= "<td><font color=".$vmxcolor.">".$vmxzero."</font></td>";
		$html_txt_arr[$sub_heading] .= "<td><font color=".$vmxcolor.">".$vmxone."</font></td>";
		$html_txt_arr[$sub_heading] .= "<td><font color=".$vmxcolor.">".$vmxtwo."</font></td>";
		// Has the extension followme enabled?
		    $fm = "<img src=\"images/cancel.png\" alt=\"Off\"/>";
		    $fmstate = false;
		    if( isset($ampuser['/AMPUSER/'.$exten.'/followme/ddial'])) {
			if( $ampuser['/AMPUSER/'.$exten.'/followme/ddial'] == "DIRECT" || $ampuser['/AMPUSER/'.$exten.'/followme/ddial'] == "EXTENSION") { 
			$fm = "<img src=\"images/accept.png\" alt=\"On\"/>";
			$fmstate = true;
			} else { 
			    $fm = "<img src=\"images/cancel.png\" alt=\"Off\"/>";
			    $fmstate = false;
			}
		    }
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$fm."</td>";
		// If follow-me is enabled, get the follow-me list
		if($fmstate) {
			$fmlist = str_replace("-","<br>",$ampuser['/AMPUSER/'.$exten.'/followme/grplist']);
		} else {
		    $fmlist = "";
		    }
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$fmlist."</td>";
		$fmlist = ""; // Empty the list
		// Now get CW, CF, CFB and CFU if set
		if( isset($cwsetting['/CW/'.$exten]) && $cwsetting['/CW/'.$exten] == "ENABLED" ) { 
		    $cw = "<img src=\"images/accept.png\" alt=\"On\"/>";
		} else { 
			$cw = "<img src=\"images/cancel.png\" alt=\"Off\"/>";
		};
		if( isset($cfsetting['/CF/'.$exten])) { 
		    $cf = $cfsetting['/CF/'.$exten];
		} else {
			$cf = "";
		};
		if( isset($cfbsetting['/CFB/'.$exten])) {
		    $cfb = $cfbsetting['/CFB/'.$exten];
		} else {
			$cfb = "";
		};
		if( isset($cfusetting['/CFU/'.$exten])) {
		    $cfu = $cfusetting['/CFU/'.$exten];
		} else {
			$cfu = "";
		};
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$cw."</td>";
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$cf."</td>";
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$cfb."</td>";
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$cfu."</td>";
		$html_txt_arr[$sub_heading] .= "</tr>\n";
	}
	$html_txt_arr[$sub_heading] .= "</table></div>";
}

function core_top($a, $b) {
	global $core_heading;

	if ($a == $core_heading) {
		return -1;
	} elseif ($b == $core_heading) {
		return 1;
	} elseif ($a != $b) {
		return $a < $b ? -1 : 1;
	} else {
		return 0;
	}
}

uksort($html_txt_arr, 'core_top');
if (!$quietmode) {
	//asort($module_select);
	uasort($module_select, 'core_top');
}

$html_txt_arr[$sub_heading] .= "</table></div>";
$html_txt .= implode("\n",$html_txt_arr);
echo $html_txt."</div>";
?>
