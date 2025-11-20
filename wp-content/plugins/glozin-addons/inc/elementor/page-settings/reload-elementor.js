jQuery(document).ready(function ($) {

    if (typeof elementor === undefined) {
        return;
    }

    elementor.settings.page.addChangeCallback('hide_topbar_section', function (newValue) {
        var $topbar = $('#elementor-preview-iframe').contents().find('#topbar');
        if( $topbar.length ) {
            if( newValue === 'yes' ) {
                $topbar.addClass('hidden');
            } else {
                $topbar.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('hide_campaign_bar_section', function (newValue) {
        var $campaign_bar = $('#elementor-preview-iframe').contents().find('#campaign-bar');
        if( $campaign_bar.length ) {
            if( newValue === 'yes' ) {
                $campaign_bar.addClass('hidden');
            } else {
                $campaign_bar.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('hide_header_section', function (newValue) {
        var $site_header = $('#elementor-preview-iframe').contents().find('#site-header');
        if( $site_header.length ) {
            if( newValue === 'yes' ) {
                $site_header.addClass('hidden');
            } else {
                $site_header.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('header_layout', function (newValue) {
        $e.run('document/save/update', {}).then(function () {
            elementor.reloadPreview();
        });
    });

    var header_fullwidth = '';
    elementor.settings.page.addChangeCallback('header_container', function (newValue) {
        var $site_header = $('#elementor-preview-iframe').contents().find('#site-header');

        if ( header_fullwidth === '' ) {
            if($site_header.find('.site-header__container').hasClass('container-xxl')) {
                header_fullwidth = true;
            } else {
                header_fullwidth = false;
            }
        }

        if( $site_header.length ) {
            if( newValue === 'fullwidth' ) {
                $site_header.find('.site-header__container').removeClass('container').addClass('container-xxl');
            } else if ( newValue === 'container' ) {
                $site_header.find('.site-header__container').removeClass('container-xxl').addClass('container');
            } else {
                if( header_fullwidth === true ) {
                    $site_header.find('.site-header__container').addClass('container-xxl');
                    $topbar.find('.topbar-container').addClass('container-xxl');
                } else if ( header_fullwidth === false ) {
                    $site_header.find('.site-header__container').removeClass('container-xxl').addClass('container');
                }
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('header_mobile_layout', function (newValue) {
        $e.run('document/save/update', {}).then(function () {
            elementor.reloadPreview();
        });
    });

    elementor.settings.page.addChangeCallback('header_logo_image', function (newValue) {
        var $site_header = $('#elementor-preview-iframe').contents().find('#site-header'),
            $image_logo = $site_header.find('.header-logo img.logo-dark');
        if( $site_header.length && $image_logo.length ) {
            if( newValue.url ) {
                $image_logo.attr('data-src', $image_logo.attr('src'));
                $image_logo.attr('src', newValue.url);
            } else {
                $image_logo.attr('src', $image_logo.attr('data-src'));
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }
    });

    elementor.settings.page.addChangeCallback('header_logo_image_light', function (newValue) {
        var $site_header = $('#elementor-preview-iframe').contents().find('#site-header'),
        $image_logo = $site_header.find('.header-logo img.logo-light');
        if( $site_header.length && $image_logo.length ) {
            if( newValue && newValue.url ) {
                $image_logo.attr('data-src', $image_logo.attr('src'));
                $image_logo.attr('src', newValue.url);
            } else {
                $image_logo.attr('src', $image_logo.attr('data-src'));
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }
    });

    elementor.settings.page.addChangeCallback('header_logo_text', function (newValue) {
        var $site_header = $('#elementor-preview-iframe').contents().find('#site-header'),
            $image_text = $site_header.find('.header-logo .header-logo__text');
        if( $site_header.length && $image_text.length ) {
            if( newValue ) {
                $image_text.attr('data-text', $image_text.html());
                $image_text.html(newValue);
            } else {
                $image_text.html($image_text.data('text'));
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }
    });

    elementor.settings.page.addChangeCallback('header_logo_svg', function (newValue) {
        $e.run('document/save/update', {}).then(function () {
            elementor.reloadPreview();
        });
    });

    elementor.settings.page.addChangeCallback('header_background', function (newValue) {
        var $body = $('#elementor-preview-iframe').contents().find('body');
        if( newValue !== '' ) {
            $body.addClass('header-transparent');
        } else {
            $body.removeClass('header-transparent');
        }
    });

    elementor.settings.page.addChangeCallback('header_text_color', function (newValue) {
        var $body = $('#elementor-preview-iframe').contents().find('body');
        if( newValue !== '' ) {
            $body.addClass('header-transparent-text-' + newValue);
        } else {
            $body.removeClass('header-transparent-text-light header-transparent-text-dark');
        }
    });


    elementor.settings.page.addChangeCallback('hide_page_header', function (newValue) {
        var $page_header = $('#elementor-preview-iframe').contents().find('#page-header');
        if( $page_header.length ) {
            if( newValue === 'yes' ) {
                $page_header.addClass('hidden');
            } else {
                $page_header.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('hide_page_header_title', function (newValue) {
        var $page_title = $('#elementor-preview-iframe').contents().find('#page-header .page-header__title');
        if( $page_title.length ) {
            if( newValue === 'yes' ) {
                $page_title.addClass('hidden');
            } else {
                $page_title.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('hide_page_header_breadcrumb', function (newValue) {
        var $breadcrumb = $('#elementor-preview-iframe').contents().find('#page-header .site-breadcrumb');
        if( $breadcrumb.length ) {
            if( newValue === 'yes' ) {
                $breadcrumb.addClass('hidden');
            } else {
                $breadcrumb.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('hide_page_header_description', function (newValue) {
        var $page_description = $('#elementor-preview-iframe').contents().find('#page-header .page-header__description');
        if( $page_description.length ) {
            if( newValue === 'yes' ) {
                $page_description.hide();
            } else {
                $page_description.show();
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('hide_footer_section', function (newValue) {
        var $site_footer = $('#elementor-preview-iframe').contents().find('#site-footer');
        if( $site_footer.length ) {
            if( newValue === 'yes' ) {
                $site_footer.addClass('hidden');
            } else {
                $site_footer.removeClass('hidden');
            }
        } else {
            $e.run('document/save/update', {}).then(function () {
                elementor.reloadPreview();
            });
        }

    });

    elementor.settings.page.addChangeCallback('footer_background', function (newValue) {
        $e.run('document/save/update', {}).then(function () {
            elementor.reloadPreview();
        });
    });
});