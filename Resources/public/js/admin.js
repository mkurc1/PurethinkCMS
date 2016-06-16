(function ($) {

    $(function () {
        hideTranslationTabForOneLanguage();
    });

    function hideTranslationTabForOneLanguage() {
        $('ul.a2lix_translationsLocales').each(function() {
            console.log($(this).children('li').size());
            if ($(this).children('li').size() < 2) {
                $(this).hide();
            }
        });
    }

})(jQuery);