jQuery(document).ready(function ($) {
    // Tabs
    var $tabsWrapper = $('#pp_settings_form ul.nav-tab-wrapper');
    $tabsWrapper.find('li').click(function (e) {
        e.preventDefault();
        $tabsWrapper.children('li').filter('.nav-tab-active').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.pp-options-wrapper > div').hide();
        var panel = $(this).find('a').first().attr('href');
        $(panel).show();
    });

    // todo: pass img url variable, title
    if (ppCoreSettings.displayHints) {
        $('.pp-options-table tr').each(function(i,e) {
            if ($(this).find('td .pp-subtext, td .pp-hint').length) {
                var img_html = '<img class="pp-show-hints" title="See more configuration tips..." src="' + ppCoreSettings.hintImg + '" />';
                
                if ($(e).find('div.pp-extra-heading').length) {
                    $(e).find('div.pp-extra-heading').before(img_html);
                } else {
                    if ($(e).find('> th').length) {
                        $(e).find('> th').append(img_html);
                    } else {
                        $(e).find('> td').first().find('span').first().append(img_html);
                    }
                }
            }
        });

        $('.pp-options-table tr img.pp-show-hints').click(function() {
            $(this).closest('tr').find('td .pp-subtext, td .pp-hint').show();
            $(this).hide();
        });
    }
});