(function ($) {
	"use strict";

	var currentFiles = null;

	function upload() {
        $('#glozin-customer-reviews-files').on( 'change', function () {
			$('.glozin-customer-reviews__items').empty();
			$('.glozin-customer-reviews__message').removeClass( 'error' );
			$('.glozin-customer-reviews__message').text( glozinCRA.message );

			let allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

			if( glozinCRA.upload_video ) {
				allowedTypes   = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'video/mp4', 'video/mpeg', 'video/ogg', 'video/webm', 'video/quicktime', 'video/x-msvideo'];
			}

			let uploadFiles    = $('#glozin-customer-reviews-files');

			var error = glozinCRA.error,
				label = glozinCRA.label,
				check = false;

			if( currentFiles ) {
				const dataTransfer = new DataTransfer();

				for ( let i = 0; i < currentFiles.length; i++ ) {
					dataTransfer.items.add(currentFiles[i]);
				}

				for ( let i = 0; i < uploadFiles[0].files.length; i++ ) {
					dataTransfer.items.add(uploadFiles[0].files[i]);
				}

				if( dataTransfer.files.length > glozinCRA.limit ) {
					$('.glozin-customer-reviews__message').addClass( 'error' );
					$('.glozin-customer-reviews__message').text( error.too_many );
					uploadFiles[0].files = currentFiles;
				} else {
					uploadFiles[0].files = dataTransfer.files;

					if( $(this).closest('.glozin-customer-reviews').find( '.glozin-customer-reviews__message' ).hasClass( 'error') ) {
						$(this).closest('.glozin-customer-reviews').find( '.glozin-customer-reviews__message' ).removeClass( 'error' );
						$(this).closest('.glozin-customer-reviews').find( '.glozin-customer-reviews__message' ).text( label );
					}

					for(let i = 0; i < dataTransfer.files.length; i++) {
						if( ! allowedTypes.includes( dataTransfer.files[i].type ) ) {
							$('.glozin-customer-reviews__message').addClass( 'error' );
							$('.glozin-customer-reviews__message').text( error.file_type );
							check = true;
							break;
						}
						
						if( dataTransfer.files[i].size && dataTransfer.files[i].size > glozinCRA.size ) {
							$('.glozin-customer-reviews__message').addClass('error');
							$('.glozin-customer-reviews__message').text( error.file_size );
							check = true;
							break;
						}
					}

					if( check ) {
						uploadFiles[0].files = currentFiles;
					} else {
						currentFiles = uploadFiles[0].files;
					}
				}
			} else {
				currentFiles = uploadFiles[0].files;
			}

			let countFiles     = uploadFiles[0].files.length;
			let countUploaded  = $('.glozin-customer-reviews__items .glozin-customer-reviews__item').length;

			if( countFiles + countUploaded > glozinCRA.limit ) {
				$('.glozin-customer-reviews__message').addClass( 'error' );
				$('.glozin-customer-reviews__message').text( error.too_many );
				$('.glozin-customer-reviews__items .glozin-customer-reviews__item').not( '.uploaded' ).remove();
				uploadFiles.val('');
				return;
			}

			for(let i = 0; i < countFiles; i++) {
				if( ! allowedTypes.includes( uploadFiles[0].files[i].type ) ) {
					$('.glozin-customer-reviews__message').addClass( 'error' );
					$('.glozin-customer-reviews__message').text( error.file_type );
					$('.glozin-customer-reviews__items .glozin-customer-reviews__item').not( '.uploaded' ).remove();
					uploadFiles.val('');
					return;
				} else if( uploadFiles[0].files[i].size && uploadFiles[0].files[i].size > glozinCRA.size ) {
					$('.glozin-customer-reviews__message').addClass('error');
					$('.glozin-customer-reviews__message').text( error.file_size );
					$('.glozin-customer-reviews__items .glozin-customer-reviews__item').not( '.uploaded' ).remove();
					uploadFiles.val('');
					return;
				} else {
					if( -1 === uploadFiles[0].files[i].type.indexOf( 'image' ) && ! glozinCRA.upload_video ) {
						continue;
					}

					let container = $('<div/>', { class: 'glozin-customer-reviews__item' });

					if( -1 === uploadFiles[0].files[i].type.indexOf( 'image' ) ) {
						container.append( $('<img>', { id: 'image-preview-' + i, src: $(this).closest( '.glozin-customer-reviews' ).find( '[name="thumbnail_url"]' ).val() } ) );
						container.append('<span class="glozin-svg-icon glozin-svg-icon--play glozin-customer-reviews__play"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21" fill="currentColor"><path d="M18 10.5L0.749999 20.4593L0.75 0.540707L18 10.5Z" fill="currentColor"></path></svg></span>');
					} else{
						container.append( $('<img>', { id: 'image-preview-' + i } ) );
						preview_image( uploadFiles[0].files, i );
					}
					
					container.append('<div class="glozin-customer-reviews__bg"></div><span class="glozin-svg-icon glozin-svg-icon--close glozin-customer-reviews__delete"><svg aria-hidden="true" role="img" focusable="false" fill="currentColor" width="16" height="16" viewBox="0 0 16 16"><path d="M16 1.4L14.6 0L8 6.6L1.4 0L0 1.4L6.6 8L0 14.6L1.4 16L8 9.4L14.6 16L16 14.6L9.4 8L16 1.4Z" fill="currentColor"></path></svg></span>');

					$('.glozin-customer-reviews__items').append( container );
				}
			}
		});
    }

    function delete_file() {
        $('.glozin-customer-reviews__items').on( 'click', '.glozin-customer-reviews__delete, .glozin-customer-reviews__bg', function () {
			var input = $(this).closest('.glozin-customer-reviews').find('#glozin-customer-reviews-files')[0],
				index = $(this).closest('.glozin-customer-reviews__item').index(),
				$label = $(this).closest('.glozin-customer-reviews').find( '.glozin-customer-reviews__message' ),
				label  = glozinCRA.label;
				
			if( ! input.files || ! input.files[index] ) {
				return false;
			}

			const dataTransfer = new DataTransfer();

			// Add all files except the one to remove
			for (let i = 0; i < input.files.length; i++) {
				if ( input.files[i].name !== input.files[index].name ) {
					dataTransfer.items.add(input.files[i]);
				}
			}
			
			if( dataTransfer.files ) {
				input.files = dataTransfer.files;
				currentFiles = input.files;
				$(this).closest('.glozin-customer-reviews__item').remove();

				if( $label.hasClass( 'error') ) {
					$label.removeClass( 'error' );
                    $label.text( label );
				}
			}
		});
    }

	function preview_image( files, index ) {
		var oFReader = new FileReader();
		oFReader.readAsDataURL(files[index]);

		oFReader.onload = function (oFREvent) {
			document.getElementById("image-preview-" + index).src = oFREvent.target.result;
		};
	};

	function lightBox() {
		$('.glozin-customer-reviews__attachment').on( 'click', 'a', function (e) {
			e.preventDefault();

			var pswpElement = $( '.pswp' )[0],
				items          = getGalleryItems( $(this).closest('.glozin-customer-reviews__attachments').find( '.glozin-customer-reviews__attachment' ) ),
				index          = $(this).closest( '.glozin-customer-reviews__attachment' ).index();

			var options = $.extend( {
				index: index,
				addCaptionHTMLFn: function( item, captionEl ) {
					if ( ! item.title ) {
						captionEl.children[0].textContent = '';
						return false;
					}
					captionEl.children[0].textContent = item.title;
					return true;
				}
			}, wc_single_product_params.photoswipe_options );

			// Initializes and opens PhotoSwipe.
			var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
			photoswipe.init();

			return false;
		});
	}

	function getGalleryItems( $slides ) {
		var items = [];

		if ( $slides.length > 0 ) {
			$slides.each( function( i, el ) {
				if( $( el ).data( 'type' ) == 'image' ) {
					var img = $( el ).find( 'img' );

					if ( img.length ) {
						var large_image_src = img.attr( 'src' ),
							large_image_w   = img.attr( 'width' ),
							large_image_h   = img.attr( 'height' ),
							alt             = img.attr( 'alt' ),
							item            = {
								alt  : alt,
								src  : large_image_src,
								w    : large_image_w,
								h    : large_image_h,
								title: img.attr( 'data-caption' ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
							};
						items.push( item );
					}
				} else {
					var video_src = $( el ).find( 'a' ).attr( 'href' );

					if( video_src.length ) {
						var item = {
                            html: '<div class="pswp__video"><video width="960" src="'+ video_src +'" controls></video></div>'
                        };
                        items.push( item );
					}
				}
			} );
		}

		return items;
	}

	/**
	 * Document ready
	 */
	$(function () {
		if ( typeof glozinCRA === 'undefined' ) {
			return false;
		}
		
		$('#commentform').attr('enctype', "multipart/form-data");

        upload();
        delete_file();
		lightBox();
    });

})(jQuery);