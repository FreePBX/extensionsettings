<?php if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); } ?>

<div class="container-fluid">
	<h1><?php echo _("FreePBX Extension Settings")?></h1>
	<div class="display full-border">
		<div class="row">
			<div class="col-sm-12">
				<div class="fpbx-container">
					<div class="display no-border">
						<?php echo $extensionsettings->showPage('grid'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>