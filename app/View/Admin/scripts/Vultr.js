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
            this.setupDb = function () {
                var name = _this.$scope.session.site.domain.replace(/\.(\w+)$/, '');
                _this.$ui.popupUrl('/database-popup.html', true, null, { ctrl: _this, settings: _this.$scope.settings, name: name });
            };
            this.format = function () {
                _this.$ui.confirm('Any existing data on remote database will be lost! Are you sure?', 'Yes', 'No').then(function () {
                    _this.$scope.config.save().then(function () {
                        var form = $('form[name="dbForm"]');
                        form.attr('action', '/admin/vultr/db-format?tweak=true');
                        form.submit();
                    });
                });
            };
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
