window.SUP || (SUP = {});

SUP.init = function() {
    SUP.vars = {
        $doc: $(document),
        $body: $('body')
    };
    SUP.common.init();
    if (SUP.features) {
        SUP.features.init();
    }
};

$(document).ready(SUP.init);