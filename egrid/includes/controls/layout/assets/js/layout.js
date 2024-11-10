(function($){
    "user strict";
    
	window.addEventListener('elementor/init', () => {

	    var EGridLayoutControlView = elementor.modules.controls.BaseData.extend({
	        onReady() {
	            var self = this;
	            var options = this.ui.radio;
	            options.each(function(key, value) {
	                $(value).on("click", function() {
	                    options.each(function(key2, value2) {
	                        if ($(value2).parent().hasClass("selected")) {
	                            $(value2).parent().removeClass("selected");
	                        }
	                    });
	                    $(this).parent().addClass("selected");
	                });
	            });
	        },
	        saveValue() {
	            var self = this;
	            var options = this.ui.radio;
	            $.each(options, function(key, value) {
	                if ($(value).is(':checked')) {
	                    self.setValue($(value).val());
	                }
	            });
	        },
	        onBeforeDestroy() {
	            this.saveValue();
	        }
	    });

	    elementor.addControlView('egrid-layout-control', EGridLayoutControlView);
	});

}(jQuery));