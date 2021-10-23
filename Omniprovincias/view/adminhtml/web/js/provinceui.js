define(["jquery", "uiRegistry", "Magento_Ui/js/form/element/select"], function (
  jquery,
  registry,
  select
) {
  "use strict";
  return select.extend({
    initialize: function () {
      var country = registry.get("index=country");
      var province_button = registry.get("index=province_button");

      var status = this._super().initialValue;

      if (status == 1) {
        field1.show();
        field2.show();
      } else {
        field2.hide();
        field1.hide();
      }
      return this;
    },
  });
});
