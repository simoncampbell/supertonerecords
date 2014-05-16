window.SUP || (SUP = {});

(function (window, document) {

    var _features = SUP.features = {
        init: function(feat) {
            var features = feat || SUP.vars.$body.data('features');
            var featuresArray = [];
            if (features) {
                featuresArray = features.split(' ');
                for (var i = 0, length = featuresArray.length; i < length; i++) {
                    var func = featuresArray[i];
                    if (_features[func] && typeof _features[func].init === 'function') {
                        _features[func].init();
                    }
                }
            }
        }
    };

    var _masonry = _features.masonry = {
        
        init: function() {

            var $container = $('.js-tiles');

            var jRes = jRespond([{
                label: 'big',
                enter: 600,
                exit: 10000
            }]);

            // We want the masonry JS to only kick in above 600px and
            // to be destroyed if it goes below that

            jRes.addFunc({
                breakpoint: 'big',

                enter: function () {

                    $container.imagesLoaded(function() {
                        $container.isotope({
                            // options
                            itemSelector: '.m-box',
                            layoutMode: 'masonry',
                            masonry: {
                                columnWidth: 255,
                                gutter: 30
                            }
                        });
                    });
                },

                exit: function() {

                    $container.isotope('destroy');

                }
            });

        },

    };

    var _tabs = _features.tabs = {

        init: function() {

            _tabs.bindUIEvents();
        },

        bindUIEvents: function() {

            SUP.vars.$body.on("click", '[data-tab]', _tabs.handleTab);
        },

        handleTab: function(e) {

            var $tab = $(this),
                $tabItem = $(this).parent('li'),
                $tabContent = $('[data-tab-content=' + $(this).data('tab') + ']');

            $tabItem.siblings().removeClass('is-active');
            $tabItem.addClass('is-active');

            $tabContent.addClass('is-active');
            $tabContent.siblings('[data-tab-content]').removeClass('is-active');



            // $(this).siblings('[data-content=' + $(this).data('tab') + ']').addClass('is-active').siblings('[data-content]').removeClass('is-active');
            
            e.preventDefault();

        }
    };

})(window, document);