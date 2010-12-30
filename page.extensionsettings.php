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
// same for VmX, enable or disable, or if time permits, change destinations, who knows, time is the linit
// Change the table so it has colors, perhaps make it scrollable with the headers intact??
// and a dozen other things that I can think of.
//

$dispnum = 'extensionsettings';
global $active_modules;

$html_txt = '<div class="content">';

if (!$extdisplay) {
	$html_txt .= '<br><h2>'._("FreePBX Extension Settings").'</h2>';
}

$full_list = framework_check_extension_usage(true);
foreach ($full_list as $key => $value) {

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
	$html_txt_arr[$sub_heading] .=  "<tr><td><strong>Extension</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td colspan=\"5\" align=\"center\"><strong>VmX Locator</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td colspan=\"2\" align=\"center\"><strong>Follow-Me</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td colspan=\"4\" align=\"center\"><strong>Call status</strong></td>";
	$html_txt_arr[$sub_heading] .=  "</tr><td></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>Status</strong></td>";	
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>1 Busy</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>1 Unavailable</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>2 Busy</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>2 Unavailable</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>FM</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>FM-list</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>CW</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>CF</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>CFB</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><strong>CFU</strong></td></tr>\n";

	foreach ($value as $exten => $item) {
		$description = explode(":",$item['description'],2);	
		$html_txt_arr[$sub_heading] .= "<tr><td><a href=\"".$item['edit_url']."\" class=\"info\">".$exten."<span>".(trim($description[1])==''?$exten:$description[1])."</span></a></td>";
		// Is VmX enabled?
		if ( $astman->database_get("AMPUSER",$exten."/vmx/busy/state") == "enabled" ) { 
		    $color = "\"BLACK\"";
		    $vmxstate = "On";
		} else { 
		    $color = "\"GREY\"";
		    $vmxstate = "Off";
		} ;
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$vmxstate."</td>";
		$html_txt_arr[$sub_heading] .= "<td><font color=".$color.">".$astman->database_get("AMPUSER",$exten."/vmx/busy/1/ext")."</font></td>";
		$html_txt_arr[$sub_heading] .= "<td><font color=".$color.">".$astman->database_get("AMPUSER",$exten."/vmx/unavail/1/ext")."</font></td>";
		$html_txt_arr[$sub_heading] .= "<td><font color=".$color.">".$astman->database_get("AMPUSER",$exten."/vmx/busy/2/ext")."</font></td>";
		$html_txt_arr[$sub_heading] .= "<td><font color=".$color.">".$astman->database_get("AMPUSER",$exten."/vmx/unavail/2/ext")."</font></td>";
		// Has the exten followme enabled?
		$followme = $astman->database_get("AMPUSER",$exten."/followme/ddial"); 
		if( isset($followme)) {
		    if($followme == "DIRECT" || $followme == "EXTENSION") { 
			$fm = "On"; 
		    } else { 
			$fm = "Off"; 
		    }
		} // end isset
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$fm."</td>";
		// If follow-me is enabled, get the follow-me list
		if($fm == "On") {
		    $fmlist = $astman->database_get("AMPUSER",$exten."/followme/grplist");
			$fmlist = str_replace("-","<br>",$fmlist);
		    } else {
			$fmlist = "";
			}
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$fmlist."</td>";
		$fmlist = ""; // Empty the list
		if( $astman->database_get("CW",$exten) == "ENABLED" ) { 
		    $cw = "On"; 
		    } else { 
			$cw = "Off"; 
		};
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$cw."</td>";
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$astman->database_get("CF",$exten)."</td>";
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$astman->database_get("CFB",$exten)."</td>";
		$html_txt_arr[$sub_heading] .= "<td align=\"center\">".$astman->database_get("CFU",$exten)."</td>";
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
