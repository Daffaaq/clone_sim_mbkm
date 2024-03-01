;(function() {
  // bootstrap/scss/_variables.scss -> $zindex-modal
  var zIndexModal = 1050

  jQuery(document).on('show.bs.modal', '.modal', function(e) {
    var visibleModalsCount = jQuery('.modal:visible').length
    var zIndex = zIndexModal + (100 * visibleModalsCount)
    jQuery(e.target).css('z-index', zIndex)
    //setImmediate(function() {
    setTimeout(function() {
      jQuery('.modal-backdrop')
        .not('.modal-stack')
        .first()
        // bootstrap/scss/_variables.scss -> $zindex-modal-backdrop
        .css('z-index', zIndex - 10)
        .addClass('modal-stack')
    },0)

  })

  jQuery(document).on('hidden.bs.modal', '.modal', function() {
    if (jQuery('.modal:visible').length) {
      jQuery.fn.modal.Constructor.prototype._checkScrollbar()
      jQuery.fn.modal.Constructor.prototype._setScrollbar()
      jQuery('body').addClass('modal-open')
    }
  })
})();