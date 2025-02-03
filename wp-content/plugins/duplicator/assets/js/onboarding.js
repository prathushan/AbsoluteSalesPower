/**
 * Duplicator Dismissible Notices.
 *
 */

'use strict';

var DupOnboarding = window.DupOnboarding || (function (document, window, $) {

    /**
     * Public functions and properties.
     */
    var app = {

        /**
         * Start the engine.
         */
        init: function () {
            $(app.ready);
        },

        /**
         * Document ready.
         */
        ready: function () {
            app.events();
        },

        /**
         * Dismissible notices events.
         */
        events: function () {
            $('[data-tooltip!=""]').qtip({
                content: {
                    attr: 'data-tooltip',
                    title:  function() { 
                        if ($(this)[0].hasAttribute("data-tooltip-title")) {
                            return  $(this).data('tooltip-title');
                        } else {
                            return false;
                        }
                    }
                },
                style: {
                    classes: 'qtip-light qtip-rounded qtip-shadow',
                    width: 500
                },
                position: {
                    my: 'top left',
                    at: 'bottom center'
                }
            });

            $(document).on('click', '#enable-usage-stats-btn', function () {
                let btn = $(this);
                $.ajax(
                    {
                        url: duplicator_onboarding.ajax_url,
                        type: "POST",
                        data: {
                            action: 'duplicator_enable_usage_stats',
                            nonce: duplicator_onboarding.nonce,
                            email: duplicator_onboarding.email,
                        },
                        beforeSend: function () {
                            // Show spinner
                            btn.find('i.fas').replaceWith('<i class="fas fa-spinner fa-spin"></i>');
                        },
                        success: function (response) {
                            if (response.success) {
                                btn.find('i.fas').replaceWith('<i class="fas fa-check"></i>');
                                //wait to display checkmark before redirecting
                                setTimeout(function () {
                                    window.location.href = duplicator_onboarding.redirect_url;
                                }, 1000);
                            } else {
                                btn.find('i.fas').replaceWith('<i class="fas fa-times"></i>');
                                //wait to display X (fail) sign before reverting back to arrow
                                setTimeout(function () {
                                    btn.find('i.fas').replaceWith('<i class="fas fa-arrow-right"></i>');
                                }, 1000);
                            }
                        },
                        fail: function () {
                            btn.find('i.fas').replaceWith('<i class="fas fa-times"></i>');
                            //wait to display X (fail) sign before reverting back to arrow
                            setTimeout(function () {
                                btn.find('i.fas').replaceWith('<i class="fas fa-arrow-right"></i>');
                            }, 1000);
                        }
                    },
                );
            });

            $(document).on( 'click', '.terms-list-toggle', function () {
                $(this).next('.terms-list').slideToggle();
                $(this).find('i.fas').toggleClass('fa-chevron-right fa-chevron-down');
            });
        },
    };

    return app;

}(document, window, jQuery));

// Initialize.
DupOnboarding.init();
