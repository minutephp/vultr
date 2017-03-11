<div class="content-wrapper ng-cloak" ng-app="vultrConfigApp" ng-controller="vultrConfigController as mainCtrl" ng-init="init()">
    <div class="admin-content">
        <section class="content-header">
            <h1>
                <span translate="">Vultr settings</span>
            </h1>

            <ol class="breadcrumb">
                <li><a href="" ng-href="/admin"><i class="fa fa-dashboard"></i> <span translate="">Admin</span></a></li>
                <li class="active"><i class="fa fa-cog"></i> <span translate="">Vultr settings</span></li>
            </ol>
        </section>

        <section class="content">
            <form class="form-horizontal" name="vultrForm" ng-submit="mainCtrl.save()">
                <div class="box box-{{vultrForm.$valid && 'success' || 'danger'}}">
                    <div class="box-header with-border">
                        <span><span translate="">Setup</span> Vultr</span>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="api_key"><span translate="">Vultr API Key:</span></label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="api_key" placeholder="Enter Vultr API Key" ng-model="settings.api_key" ng-required="true" minlength="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="database"><span translate="">Database server:</span></label>
                            <div class="col-sm-9">
                                <button type="button" class="btn btn-flat btn-default btn-sm" ng-click="mainCtrl.setupDb()">
                                    <i class="fa fa-database"></i> <span translate="">Setup database server..</span>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span translate="">Cron daemon:</span></label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" ng-model="settings.cron" ng-value="'shared'"> <span translate="">Shared server</span>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" ng-model="settings.cron" ng-value="'standalone'"> <span translate="">Standalone server</span>
                                </label>
                                <p class="help-block" ng-show="settings.cron === 'standalone'">
                                    <span translate=""><i class="fa fa-exclamation-triangle"></i> An additional server will be required to run cron jobs.</span>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span translate="">Server concurrency:</span></label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" ng-model="settings.clear_dns" ng-value="true"> <span translate="">Single server</span>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" ng-model="settings.clear_dns" ng-value="false"> <span translate="">Multiple instances (DNS load balancer)</span>
                                </label>
                                <p class="help-block" ng-show="settings.clear_dns"><span translate=""><i class="fa fa-exclamation-triangle"></i> All previous "A" records will be removed!</span></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span translate="">Deployment:</span></label>
                            <div class="col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" ng-model="settings.dryRun" ng-value="false"> <span translate="">Automatic</span>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" ng-model="settings.dryRun" ng-value="true"> <span translate="">Manual</span>
                                </label>

                                <p class="help-block" ng-show="settings.dryRun === true">
                                    <i class="fa fa-exclamation-triangle"></i> <span translate="">You will have to deploy the server yourself</span>
                                </p>
                            </div>
                        </div>

                    </div>

                    <div class="box-footer with-border">
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-flat btn-primary">
                                    <i class="fa fa-cloud-upload"></i> <span translate="">Deploy to Vultr cloud</span> <i class="fa fa-angle-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>

    <script type="text/ng-template" id="/database-popup.html">
        <div class="box">
            <div class="box-header with-border">
                <b class="pull-left"><span translate="">Setup database</span></b>
                <a class="pull-right close-button" href=""><i class="fa fa-times"></i></a>
                <div class="clearfix"></div>
            </div>

            <form class="form-horizontal" name="dbForm" method="POST" ng-submit="return false">
                <div class="box-body">
                    <div class="form-group" ng-init="settings.database.name = settings.database.name || name">
                        <label class="col-sm-3 control-label" for="name"><span translate="">Db Name:</span></label>
                        <div class="col-sm-9">
                            <input type="text" auto-focus class="form-control" id="name" placeholder="Enter Name" ng-model="settings.database.name" ng-required="true">
                        </div>
                    </div>
                    <div class="form-group" ng-init="settings.database.username = settings.database.username || name">
                        <label class="col-sm-3 control-label" for="username"><span translate="">Username:</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" placeholder="Enter Username" ng-model="settings.database.username" ng-required="true">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="password"><span translate="">Password:</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="password" placeholder="Enter Password" ng-model="settings.database.password" ng-required="true">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="host"><span translate="">Host:</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="host" placeholder="Enter Host" ng-model="settings.database.host" ng-required="true">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"><span translate="">Setup Db:</span></label>
                        <div class="col-sm-9">
                            <p class="help-block">
                                <button type="button" class="btn btn-flat btn-danger btn-xs" ng-click="ctrl.format()">
                                    <i class="fa fa-exclamation-triangle"></i> <span translate="">Format database?</span>
                                </button>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="box-footer with-border">
                    <button type="button" class="btn btn-flat btn-primary pull-right" ng-disabled="!dbForm.$valid" ng-click="hide()">
                        <i class="fa fa-check-circle"></i> <span translate>Close</span>
                    </button>
                </div>
            </form>
        </div>
    </script>

</div>
