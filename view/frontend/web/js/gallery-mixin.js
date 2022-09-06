define(['Hryvinskyi_SeoImageOptimizerFrontendUi/js/action/check-is-webp-supported'], function (checkIsWebpSupported) {
    'use strict';
    return function (target) {
        return target.extend({
            initialize: function (config, element) {
                if (checkIsWebpSupported() && config.data !== undefined) {
                    config.data.forEach((element, key) => {
                        if (element['full_webp']) {
                            config.data[key]['full'] = element['full_webp'];
                        }
                        if (element['img_webp']) {
                            config.data[key]['img'] = element['img_webp'];
                        }
                        if (element['thumb_webp']) {
                            config.data[key]['thumb'] = element['thumb_webp'];
                        }
                    });
                }

                this._super(config, element);
            }
        });
    }
});
