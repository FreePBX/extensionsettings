<?php if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); } ?>

<table
    id="set_table"
    data-url="ajax.php?module=extensionsettings&command=list"
    data-cache="false"
    data-toggle="table"
    data-show-columns="true"
    data-pagination="false"
    data-show-refresh="true"
    data-search="true"
    data-resizable="true"
    data-sortable="true"
    class="table table-striped">
    <thead>
        <tr>
            <th data-field="exten" data-formatter="ExtensionsFormat" data-sortable="true" class="col-md-3" rowspan="2"><?php echo _("Extension"); ?></th>
            <th colspan="7" class="col-md-4"><?php echo _("VmX Locator"); ?></th>
            <th colspan="2" class="col-md-2"><?php echo _("Follow-Me"); ?></th>
            <th colspan="5" class="col-md-3"><?php echo _("Call status"); ?></th>
        </tr>
        <tr>
            <th data-field="vmxstate" data-formatter="OnOffFormat" data-sortable="true" class="text-center"><?php echo _("Status"); ?></th>
            <th data-field="vmxbusy" data-formatter="OnOffFormat" data-sortable="true" class="text-center"><?php echo _("Busy"); ?></th>
            <th data-field="vmxunavail" data-formatter="OnOffFormat" data-sortable="true" class="text-center"><?php echo _("Unavail"); ?></th>
            <th data-field="vmxoperator" data-formatter="OnOffFormat" data-sortable="true" class="text-center"><?php echo _("Operator"); ?></th>
            <th data-field="vmxzero" data-sortable="true"><?php echo _("Press 0"); ?></th>
            <th data-field="vmxone" data-sortable="true"><?php echo _("Press 1"); ?></th>
            <th data-field="vmxtwo" data-sortable="true"><?php echo _("Press 2"); ?></th>
            <th data-field="fm" data-formatter="OnOffFormat" data-sortable="true" class="text-center"><?php echo _("FM"); ?></th>
            <th data-field="fmlist" data-formatter="FMListFormat" data-sortable="true"><?php echo _("FM-list"); ?></th>
            <th data-field="cw" data-formatter="OnOffFormat" data-sortable="true" class="text-center"><?php echo _("CW"); ?></th>
            <th data-field="dnd" data-formatter="OnOffFormat" data-sortable="true" class="text-center"><?php echo _("DND"); ?></th>
            <th data-field="cf" data-sortable="true" class="text-center"><?php echo _("CF"); ?></th>
            <th data-field="cfb" data-sortable="true" class="text-center"><?php echo _("CFB"); ?></th>
            <th data-field="cfu" data-sortable="true" class="text-center"><?php echo _("CFU"); ?></th>
        </tr>
    </thead>
</table>