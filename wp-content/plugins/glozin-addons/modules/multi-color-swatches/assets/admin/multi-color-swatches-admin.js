jQuery(document).ready(function($) {
    $(document.body).on('change', '[name="wcboost_variation_swatches_is_dual_color"]', function() {
        var $this = $(this);
        var $condition = $this.closest('.form-field').siblings('.condition--is-dual-color');

        if ( $this.val() == 'yes' ) {
            $condition.removeClass('hidden');
        } else {
            $condition.addClass('hidden');
        }
    });
});