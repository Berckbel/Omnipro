var config = {
  map: {
    "*": {
      ajaxQty: "Omnipro_Carritoajax/js/cartQtyUpdate",
      "Magento_Checkout/js/sidebar": "Omnipro_Carritoajax/js/sidebar",
    },
  },

  config: {
    mixins: {
      "Magento_Checkout/js/sidebar": {
        "Omnipro_Carritoajax/js/sidebar-mixin": true,
      },
    },
  },
};
