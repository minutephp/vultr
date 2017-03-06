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
                                <input type="text" class="form-control" id="api_key" placeholder="Enter Vultr API Key" ng-model="settings.api_key" ng-required="true" minlength="1">
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
</div>
