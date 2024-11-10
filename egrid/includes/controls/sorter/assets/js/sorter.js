(function($) {
    "user strict";

    window.addEventListener('elementor/init', () => {

        var EGridSorterControlView = elementor.modules.controls.BaseData.extend({
            onReady() {
                var self = this;
                var options = this.ui.radio;
                var el = self.$el;
                var cid = self.model.cid;
                var sortableLists = el.find(".connected-sortable");
                sortableLists.sortable({
                    connectWith: sortableLists,
                    stop: function(event, ui) {
                        self.saveValue();
                    }
                });
            },
            saveValue() {
                var self = this;
                var el = self.$el;
                var cid = self.model.cid;
                var sortableLists = el.find(".connected-sortable");
                var sortableListsValue = {};
                sortableLists.each(function() {
                    var index = $(this).data('index');
                    var sortableListValue = [];
                    var sortableItemElIds = $(this).sortable('toArray');
                    $.each(sortableItemElIds, function(_index, sortableItemElId) {
                    	sortableListValue.push($('#' + sortableItemElId).data('value'));
                    });
                    sortableListsValue[index] = sortableListValue;
                });
                self.setValue(sortableListsValue);
            },
            onBeforeDestroy() {
                // this.saveValue();
            }
        });

        elementor.addControlView('egrid-sorter-control', EGridSorterControlView);
    });

}(jQuery));