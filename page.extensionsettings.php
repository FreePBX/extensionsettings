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
	if ($active_modules[$key]['rawname'] == 'featurecodeadmin' || ($quietmode && !isset($_REQUEST[$sub_heading_id]))) {
		continue; // we don't want any featurecodes
	}
	if ($txtdom == 'core') {
		$txtdom = 'amp';
		$active_modules[$key]['name'] = 'Extensions';
		$core_heading = $sub_heading =  dgettext($txtdom,$active_modules[$key]['name']);
	} else {
		$sub_heading =  dgettext($txtdom,$active_modules[$key]['name']);
	}
	$module_select[$sub_heading_id] = $sub_heading;
	$html_txt_arr[$sub_heading] =   "<div class=\"$sub_heading_id\"><table border=\"1\" width=\"85%\"><tr><td><br><strong>Ext.</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td><br><strong>Description</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><br><strong>VMXB1</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><br><strong>VMXB2</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><br><strong>VMXU1</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><br><strong>VMXU2</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><br><strong>CW</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><br><strong>CF</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><br><strong>CFB</strong></td>";
	$html_txt_arr[$sub_heading] .=  "<td align=\"center\"><br><strong>CFU</strong></td></tr>\n";
	
	foreach ($value as $exten => $item) {
		$html_txt_arr[$sub_heading] .= "<tr><td>".$exten."</td>";
		$description = explode(":",$item['description'],2);
		$html_txt_arr[$sub_heading] .= "<td>".(trim($description[1])==''?$exten:$description[1])."</td>";
		if ( $astman->database_get("AMPUSER",$exten."/vmx/busy/state") == "enabled" ) { } else { } ;
		$html_txt_arr[$sub_heading] .= "<td>".$astman->database_get("AMPUSER",$exten."/vmx/busy/1/ext")."</td>";
		$html_txt_arr[$sub_heading] .= "<td>".$astman->database_get("AMPUSER",$exten."/vmx/busy/2/ext")."</td>";
		$html_txt_arr[$sub_heading] .= "<td>".$astman->database_get("AMPUSER",$exten."/vmx/unavail/1/ext")."</td>";
		$html_txt_arr[$sub_heading] .= "<td>".$astman->database_get("AMPUSER",$exten."/vmx/unavail/2/ext")."</td>";
		if( $astman->database_get("CW",$exten) == "ENABLED" ) { $cw = "ON"; } else { $cw = "OFF"; };
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

if (!$quietmode) {
	$rnav_txt = '<div class="rnav"><form name="print" action="'.$_SERVER['PHP_SELF'].'?quietmode=on&'.$_SERVER['QUERY_STRING'].'" target=\"_blank\" method="post"><ul>';
	foreach ($module_select as $id => $sub) {
		$rnav_txt .= "<li><input type=\"checkbox\" value=\"$id\" name=\"$id\" id=\"$id\" class=\"disp_filter\" CHECKED /><label id=\"lab_$id\" name=\"lab_$id\" for=\"$id\">$sub</label></li>\n";
	}
	$rnav_txt .= "</ul><hr><div style=\"text-align:center\"><input type=\"submit\" value=\"".sprintf(dgettext('printextensions',_("Printer Friendly Page")))."\" /></div>\n";
	echo $rnav_txt;
?>
	<script language="javascript">
	<!-- Begin

	$(document).ready(function(){
		$(".disp_filter").click(function(){
			$("."+this.id).slideToggle();
		});
	});

	// End -->
	</script>
	</form></div>
<?php
}
echo $html_txt."</div>";
?>
