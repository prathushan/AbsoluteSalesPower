function closetabs(ids) {
	var x = ids;
	y = x.split(",");

	for(var i = 0; i < y.length; i++) {
	//console.log(y[i]);
	document.getElementById(y[i]).style.display = 'none';
	document.getElementById("id"+y[i]).classList.remove('nav-tab-active');
	}
}

function newtab(id) {
	var x = id;
	console.log(x);
	document.getElementById(x).style.display = 'block';
	document.getElementById("id"+x).classList.add('nav-tab-active');
	document.getElementById('hidden_tab_value').value=x;
}

(function( $ ) {
    'use strict';
    $( function() {
        $('.cf7pp-stripe-connect-notice, .cf7pp-ppcp-connect-notice').on('click', '.notice-dismiss', function(event, el){
            var $notice = $(this).parent('.notice.is-dismissible');
            var dismiss_url = $notice.attr('data-dismiss-url');
            if (dismiss_url) {
                $.get(dismiss_url);
            }
        });

        $('[name="mode"]').on('change', function(){
            const sandbox = parseInt($('[name="mode"]:checked').val()) === 1,
                $onboardingStartBtn = $('#cf7pp-ppcp-onboarding-start-btn'),
                onboardingUrl = $onboardingStartBtn.attr('href').split('?'),
                onboardingParams = new URLSearchParams(onboardingUrl[1] || '');

            if (sandbox) {
                onboardingParams.set('sandbox', '1');
            } else {
                onboardingParams.delete('sandbox');
            }

            onboardingUrl[1] = onboardingParams.toString();
            $onboardingStartBtn.attr('href', onboardingUrl.join('?'));
        });

        $(document).on('click', '#cf7pp-ppcp-disconnect', function(e){
            e.preventDefault();

            if (!confirm('Are you sure?')) return false;

            const $this = $(this),
                $ppcpStatusTable = $('#cf7pp-ppcp-status-table');

            if ($this.hasClass('processing')) return false;
            $this.addClass('processing');

            $ppcpStatusTable.css({'opacity': 0.5});

            $.post(cf7pp.ajaxUrl, {
                action: 'cf7pp-ppcp-disconnect',
                nonce: cf7pp.nonce,
                form_id: $(this).attr('data-form-id')
            }, function(response){
                $this.removeClass('processing');
                $ppcpStatusTable.css({'opacity': 1});

                if (response.success) {
                    $ppcpStatusTable.html(response.data.statusHtml);
                } else {
                    const message = response.data && response.data.message ?
                        response.data.message :
                        'An unexpected error occurred. Please reload the page and try again.';
                    alert(message);
                }
            });

            return false;
        });
    });
})(jQuery);