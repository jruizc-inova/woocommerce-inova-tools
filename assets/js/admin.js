jQuery( document ).ready(function() {
	function checkEnabledSettings(){
		if (jQuery('.enable_settings').is(':checked')) {
	   		jQuery('.settings_enabled_dependency input,.settings_enabled_dependency textarea,.settings_enabled_dependency select').removeClass('item_read_only');
	   	}else{
	   		jQuery('.settings_enabled_dependency input,.settings_enabled_dependency textarea,.settings_enabled_dependency select').addClass('item_read_only');
	   	}
	}

	function _init(){
		checkEnabledSettings();
	}
	
	_init();
   jQuery('.enable_settings').click(checkEnabledSettings);
});