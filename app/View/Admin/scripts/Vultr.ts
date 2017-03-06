/// <reference path="../../../../../../../public/static/bower_components/minute/_all.d.ts" />

module Admin {
    export class VultrConfigController {
        constructor(public $scope: any, public $minute: any, public $ui: any, public $timeout: ng.ITimeoutService,
                    public gettext: angular.gettext.gettextFunction, public gettextCatalog: angular.gettext.gettextCatalog) {

            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = {processors: [], tabs: {}};
            $scope.config = $scope.configs[0] || $scope.configs.create().attr('type', 'vultr').attr('data_json', {});
            $scope.settings = $scope.config.attr('data_json');
        }

        save = () => {
            this.$scope.config.save(this.gettext('Vultr saved successfully')).then(() => {
                top.location.href = '/admin/vultr/deploy';
            });
        };
    }

    angular.module('vultrConfigApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('vultrConfigController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', VultrConfigController]);
}
