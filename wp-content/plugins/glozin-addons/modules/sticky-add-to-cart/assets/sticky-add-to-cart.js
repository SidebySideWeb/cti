(function ($) {
	'use strict';

	var glozin = glozin || {};

	glozin.init = function () {
        this.stickyAddToCart();
        this.addToCartFormScroll();
        this.changeAttributesData();

        $(document.body).on('glozin_off_canvas_closed glozin_modal_closed glozin_popover_closed', function (e) {
            glozin.stickyAddToCart();
        });
    }

    glozin.stickyAddToCart = function() {
        var $selector = $( '#glozin-sticky-add-to-cart' );
        if ( !$selector.length ) {
            return;
        }

        var headerHeight = 0;

        if ( $(document.body).hasClass( 'admin-bar' ) ) {
            headerHeight += 32;
        }
        function stickyATCToggle() {
            if( ! $('div.product').find( 'form.cart' ).length ) {
                return;
            }
            
            var cartHeight = $('div.product').find( 'form.cart' ).offset().top + $('div.product').find( 'form.cart' ).outerHeight() - headerHeight;

            if ( window.scrollY > cartHeight ) {
                $selector.addClass( 'open' );
                $(document.body).addClass('glozin-atc-sticky-height-open');
            } else {
                $selector.removeClass( 'open' );
                $(document.body).removeClass('glozin-atc-sticky-height-open');
            }

        }

        $(window).on( 'scroll', function () {
            stickyATCToggle();
        } );

        $(window).on( 'resize', function () {
            var height = $selector.height();

            if( $(document.body).find( 'div.product' ).hasClass( 'outofstock' ) ) {
                height = 0;
            }

            $(document.body).css('--gz-atc-sticky-height', height + 'px');
        } ).trigger('resize');
    }

    glozin.addToCartFormScroll = function() {
        //sticky-atc-button
        $( '#glozin-sticky-add-to-cart' ).on( 'click', '.gz-add-to-cart-options', function ( event ) {
            event.preventDefault();

            $( 'html,body' ).stop().animate({
                scrollTop: $('div.product').find( 'form.cart' ).offset().top - 50
            },
            'slow');
        });
    }

    glozin.changeAttributesData = function() {
        var $selector = $( '#glozin-sticky-add-to-cart' );
        var $mainATCForm = $('div.product').find('form.cart');
        if ( !$selector.length ) {
            return;
        }
        var $image = $selector.find( '.glozin-sticky-atc__image img' ),
            $addToCart = $selector.find('.single_add_to_cart_button'),
            $addToCart_text = $addToCart.text(),
            syncing = false;

        $selector.find( '.glozin-sticky-atc__variations' ).on('change', 'select', function (e) {
            var optionSelected = $("option:selected", this),
                imageSelected = optionSelected.data('image'),
                attributes = optionSelected.data('attributes'),
                stock = optionSelected.data('stock');

            if( attributes !== undefined ) {
                $addToCart.removeClass('disabled');
                for( var key in attributes) {
                    $selector.find('form.cart').find('input[name="'+ key +'"]').val( attributes[key] );
                    if ( syncing ) {
                        continue;
                    }
                    syncing = true;
                    var mainSelected = $mainATCForm.find('select[name="'+ key +'"]');
                    if( mainSelected.length > 0  ) {
                        mainSelected.val( attributes[key] ).trigger('change');
                    }
                    syncing = false;
                }
            } else {
                $addToCart.addClass('disabled');
            }

            if( stock ) {
                if( stock.stock == 'out_of_stock' ) {
                    $addToCart.addClass('disabled');
                }

                $addToCart.text( stock.button_text );
            } else {
                $addToCart.text( $addToCart_text );
            }

            if( imageSelected === undefined ) {
                imageSelected = $image.data('o_src');
            }
            $image.attr( 'src', imageSelected );

        });

        var $stickySelect = $selector.find( '.glozin-sticky-atc__variations' ).find('select[name="variation_id"]');
        
        $mainATCForm.on('found_variation', function (event, variation) {
            if (syncing) return;
            
            var currentAttributes = variation.attributes;
            
            var bestMatch = null;
            var bestScore = 0;
            var bestIndex = -1;

            $stickySelect.find('option').each(function(index) {
                var $option = $(this);
                var optionValue = $option.val();
                
                if (!optionValue || optionValue === '') return;
                
                var optionAttrs = $option.data('attributes');
                if (typeof optionAttrs === 'string') {
                    try {
                        optionAttrs = JSON.parse(optionAttrs);
                    } catch(e) {
                        optionAttrs = {};
                    }
                }
                
                if (!optionAttrs || typeof optionAttrs !== 'object') return;
                
                var score = 0;
                var totalAttrs = 0;
                
                $.each(currentAttributes, function(attrName, attrValue) {
                    totalAttrs++;
                    if (optionAttrs[attrName] === attrValue) {
                        score++;
                    }
                });
                
                if (score === totalAttrs && totalAttrs > 0) {
                    bestMatch = $option;
                    bestScore = score;
                    bestIndex = index;
                    return false;
                }
                
                if (score > bestScore) {
                    bestScore = score;
                    bestMatch = $option;
                    bestIndex = index;
                }
            });

            if (bestMatch && bestScore > 0) {
                syncing = true;
                $stickySelect[0].selectedIndex = bestIndex;
                $stickySelect.trigger('change');
                syncing = false;
            }
        });
        
    }
	/**
	 * Document ready
	 */
	$(function () {
		glozin.init();
        $( '#glozin-sticky-add-to-cart' ).find( '.glozin-sticky-atc__variations select' ).trigger( 'change' );
	});

})(jQuery);