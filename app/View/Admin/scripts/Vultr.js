/// <reference path="../../../../../../../public/static/bower_components/minute/_all.d.ts" />
var Admin;
(function (Admin) {
    var VultrConfigController = (function () {
        function VultrConfigController($scope, $minute, $ui, $timeout, gettext, gettextCatalog) {
            var _this = this;
            this.$scope = $scope;
            this.$minute = $minute;
            this.$ui = $ui;
            this.$timeout = $timeout;
            this.gettext = gettext;
            this.gettextCatalog = gettextCatalog;
            this.save = function () {
                _this.$scope.config.save(_this.gettext('Vultr saved successfully')).then(function () {
                    top.location.href = '/admin/vultr/deploy';
                });
            };
            gettextCatalog.setCurrentLanguage($scope.session.lang || 'en');
            $scope.data = { processors: [], tabs: {} };
            $scope.config = $scope.configs[0] || $scope.configs.create().attr('type', 'vultr').attr('data_json', {});
            $scope.settings = $scope.config.attr('data_json');
        }
        return VultrConfigController;
    }());
    Admin.VultrConfigController = VultrConfigController;
    angular.module('vultrConfigApp', ['MinuteFramework', 'AdminApp', 'gettext'])
        .controller('vultrConfigController', ['$scope', '$minute', '$ui', '$timeout', 'gettext', 'gettextCatalog', VultrConfigController]);
})(Admin || (Admin = {}));
