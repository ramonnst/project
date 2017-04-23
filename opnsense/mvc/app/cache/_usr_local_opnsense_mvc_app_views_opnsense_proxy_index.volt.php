

<script type="text/javascript">

    $( document ).ready(function() {

        var data_get_map = {'frm_proxy':"/api/proxy/settings/get"};

        // load initial data
        mapDataToFormUI(data_get_map).done(function(){
            formatTokenizersUI();
            $('.selectpicker').selectpicker('refresh');
            // request service status on load and update status box
            ajaxCall(url="/api/proxy/service/status", sendData={}, callback=function(data,status) {
                updateServiceStatusUI(data['status']);
            });
        });

        /*************************************************************************************************************
         * link grid actions
         *************************************************************************************************************/

        $("#grid-remote-blacklists").UIBootgrid(
                {   'search':'/api/proxy/settings/searchRemoteBlacklists',
                    'get':'/api/proxy/settings/getRemoteBlacklist/',
                    'set':'/api/proxy/settings/setRemoteBlacklist/',
                    'add':'/api/proxy/settings/addRemoteBlacklist/',
                    'del':'/api/proxy/settings/delRemoteBlacklist/',
                    'toggle':'/api/proxy/settings/toggleRemoteBlacklist/'
                }
        );

        // when  closing DialogEditBlacklist, point the user to the download buttons
        $("#DialogEditBlacklist").on("show.bs.modal", function () {
            // wait some time before linking the save button, missing handle
            setTimeout(function(){
                $("#btn_DialogEditBlacklist_save").click(function(){
                    $("#remoteACLchangeMessage").slideDown(1000, function(){
                        setTimeout(function(){
                            $("#remoteACLchangeMessage").slideUp(2000);
                        }, 2000);
                    });
                });
            }, 500);
        });

        /**
         *
         * Reconfigure proxy - activate changes
         */
        $("#reconfigureAct").click(function(){
            $("#reconfigureAct_progress").addClass("fa fa-spinner fa-pulse");
            ajaxCall(url="/api/proxy/service/reconfigure", sendData={}, callback=function(data,status) {
                // when done, disable progress animation.
                $("#reconfigureAct_progress").removeClass("fa fa-spinner fa-pulse");

                if (status != "success" || data['status'] != 'ok') {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_WARNING,
                        title: "Error reconfiguring proxy",
                        message: data['status'],
                        draggable: true
                    });
                }
            });
        });

        /**
         *
         * Download ACLs and reconfigure poxy - activate changes
         */
        $("#fetchandreconfigureAct").click(function(){
            $("#fetchandreconfigureAct_progress").addClass("fa fa-spinner fa-pulse");
            ajaxCall(url="/api/proxy/service/fetchacls", sendData={}, callback=function(data,status) {
                // when done, disable progress animation.
                $("#fetchandreconfigureAct_progress").removeClass("fa fa-spinner fa-pulse");
                if (status != "success" || data['status'] != 'ok') {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_WARNING,
                        title: "Error fetching remote acls",
                        message: data['status'],
                        draggable: true
                    });
                }
            });
        });

        /**
         *
         * Download ACLs, no reconfigure
         */
        $("#downloadAct").click(function(){
            $("#downloadAct_progress").addClass("fa fa-spinner fa-pulse");
            ajaxCall(url="/api/proxy/service/downloadacls", sendData={}, callback=function(data,status) {
                // when done, disable progress animation.
                $("#downloadAct_progress").removeClass("fa fa-spinner fa-pulse");
                if (status != "success" || data['status'] != 'ok') {
                    BootstrapDialog.show({
                        type: BootstrapDialog.TYPE_WARNING,
                        title: "Error fetching remote acls",
                        message: data['status'],
                        draggable: true
                    });
                }
            });
        });

        /**
         * setup cron item
         */
        $("#ScheduleAct").click(function() {
            $("#scheduleAct_progress").addClass("fa fa-spinner fa-pulse");
            ajaxCall(url="/api/proxy/settings/fetchRBCron", sendData={}, callback=function(data,status) {
                $("#scheduleAct_progress").removeClass("fa fa-spinner fa-pulse");
                if (data.uuid !=undefined) {
                    // redirect to cron page
                    $(location).attr('href',"/ui/cron/item/open/"+data.uuid);
                }
            });
        });

        // form save event handlers for all defined forms
        $('[id*="save_"]').each(function(){
            $(this).click(function() {
                var frm_id = $(this).closest("form").attr("id");
                var frm_title = $(this).closest("form").attr("data-title");
                // save data for General TAB
                saveFormToEndpoint(url="/api/proxy/settings/set",formid=frm_id,callback_ok=function(){
                    // on correct save, perform reconfigure. set progress animation when reloading
                    $("#"+frm_id+"_progress").addClass("fa fa-spinner fa-pulse");

                    //
                    ajaxCall(url="/api/proxy/service/reconfigure", sendData={}, callback=function(data,status){
                        // when done, disable progress animation.
                        $("#"+frm_id+"_progress").removeClass("fa fa-spinner fa-pulse");

                        if (status != "success" || data['status'] != 'ok' ) {
                            // fix error handling
                            BootstrapDialog.show({
                                type:BootstrapDialog.TYPE_WARNING,
                                title: frm_title,
                                message: JSON.stringify(data),
                                draggable: true
                            });
                        } else {
                            // request service status after successful save and update status box (wait a few seconds before update)
                            setTimeout(function(){
                                ajaxCall(url="/api/proxy/service/status", sendData={}, callback=function(data,status) {
                                    updateServiceStatusUI(data['status']);
                                });
                            },3000);
                        }
                    });
                });
            });
        });

        // update history on tab state and implement navigation
        if(window.location.hash != "") {
            $('a[href="' + window.location.hash + '"]').click()
        }
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            history.pushState(null, null, e.target.hash);
        });

    });


</script>

<ul class="nav nav-tabs" role="tablist"  id="maintabs">
<?php foreach ((empty($mainForm['tabs']) ? ([]) : ($mainForm['tabs'])) as $tab) { ?>
    <?php if ((empty($tab['subtabs']) ? (false) : ($tab['subtabs']))) { ?>
        

        
            <?php $active_subtab = ''; ?>
            <?php foreach ((empty($tab['subtabs']) ? ([]) : ($tab['subtabs'])) as $subtab) { ?>
                <?php if ($subtab[0] == (empty($mainForm['activetab']) ? ('') : ($mainForm['activetab']))) { ?>
                    <?php $active_subtab = $subtab[0]; ?>
                <?php } ?>
            <?php } ?>

        <li role="presentation" class="dropdown <?php if ((empty($mainForm['activetab']) ? ('') : ($mainForm['activetab'])) == $active_subtab) { ?>active<?php } ?>">
            <a data-toggle="dropdown" href="#" class="dropdown-toggle pull-right visible-lg-inline-block visible-md-inline-block visible-xs-inline-block visible-sm-inline-block" role="button" style="border-left: 1px dashed lightgray;">
                <b><span class="caret"></span></b>
            </a>
            <a data-toggle="tab" href="#subtab_<?= $tab['subtabs'][0][0] ?>" class="visible-lg-inline-block visible-md-inline-block visible-xs-inline-block visible-sm-inline-block" style="border-right:0px;"><b><?= $tab[1] ?></b></a>
            <ul class="dropdown-menu" role="menu">
                <?php foreach ((empty($tab['subtabs']) ? ([]) : ($tab['subtabs'])) as $subtab) { ?>
                <li class="<?php if ((empty($mainForm['activetab']) ? ('') : ($mainForm['activetab'])) == $subtab[0]) { ?>active<?php } ?>"><a data-toggle="tab" href="#subtab_<?= $subtab[0] ?>"><i class="fa fa-check-square"></i> <?= $subtab[1] ?></a></li>
                <?php } ?>
            </ul>
        </li>
    <?php } else { ?>
        
        <li <?php if ((empty($mainForm['activetab']) ? ('') : ($mainForm['activetab'])) == $tab[0]) { ?> class="active" <?php } ?>>
                <a data-toggle="tab" href="#tab_<?= $tab[0] ?>">
                    <b><?= $tab[1] ?></b>
                </a>
        </li>
    <?php } ?>
<?php } ?>
    
    <li><a data-toggle="tab" href="#remote_acls"><b><?= $lang->_('Remote Access Control Lists') ?></b></a></li>
</ul>

<div class="content-box tab-content">
    <?php foreach ((empty($mainForm['tabs']) ? ([]) : ($mainForm['tabs'])) as $tab) { ?>
        <?php if ((empty($tab['subtabs']) ? (false) : ($tab['subtabs']))) { ?>
            
            <?php foreach ((empty($tab['subtabs']) ? ([]) : ($tab['subtabs'])) as $subtab) { ?>
                <div id="subtab_<?= $subtab[0] ?>" class="tab-pane fade<?php if ((empty($mainForm['activetab']) ? ('') : ($mainForm['activetab'])) == $subtab[0]) { ?> in active <?php } ?>">
                    <?= $this->partial('layout_partials/base_form', ['fields' => $subtab[2], 'id' => 'frm_' . $subtab[0], 'data_title' => $subtab[1], 'apply_btn_id' => 'save_' . $subtab[0]]) ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php if ((empty($tab['subtabs']) ? (false) : ($tab['subtabs'])) == false) { ?>
            <div id="tab_<?= $tab[0] ?>" class="tab-pane fade<?php if ((empty($mainForm['activetab']) ? ('') : ($mainForm['activetab'])) == $tab[0]) { ?> in active <?php } ?>">
                <?= $this->partial('layout_partials/base_form', ['fields' => $tab[2], 'id' => 'frm_' . $tab[0], 'apply_btn_id' => 'save_' . $tab[0]]) ?>
            </div>
        <?php } ?>
    <?php } ?>
    <div id="remote_acls" class="tab-pane fade">

        <table class="table table-striped table-condensed table-responsive">
            <colgroup>
                <col class="col-md-3"/>
                <col class="col-md-9"/>
            </colgroup>
            <tbody>
            <tr>
                <td colspan="2" align="right">
                    <small><?= $lang->_('full help') ?> </small><a href="#"><i class="fa fa-toggle-off text-danger" id="show_all_help_show_all_help_frm_proxy-forward-acl-remoteACLS" type="button"></i></a>
                </td>
            </tr>
            <tr>
                <td><div class="control-label">
                    <a id="help_for_proxy.forward.acl.remoteACLs.blacklist" href="#" class="showhelp"><i class="fa fa-info-circle"></i></a>
                    <b><?= $lang->_('Remote Blacklist') ?></b>
                </div>
                </td>
                <td>
                  <small class="hidden" for="help_for_proxy.forward.acl.remoteACLs.blacklist">
                      <?= sprintf($lang->_('
                      Add an item to the table to fetch a remote acl for blacklisting.%s
                      You can enable or disable the blacklist list.%s
                      The active blacklists will be merged with the settings under %sForward Proxy -> Access Control List%s.
                      '), '<br/>', '<br/>', '<b>', '</b>') ?>
                  </small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div id="remoteACLchangeMessage" class="alert alert-info" style="display: none" role="alert">
                        <?= $lang->_('Note: after changing categories, please remember to download the ACL again to apply your new settings') ?>
                    </div>
                    <table id="grid-remote-blacklists" class="table table-condensed table-hover table-striped table-responsive" data-editDialog="DialogEditBlacklist">
                        <thead>
                        <tr>
                            <th data-column-id="enabled" data-formatter="rowtoggle" data-sortable="false"  data-width="6em"><?= $lang->_('Enabled') ?></th>
                            <th data-column-id="filename" data-type="string" data-sortable="false"  data-visible="true"><?= $lang->_('Filename') ?></th>
                            <th data-column-id="url" data-type="string" data-sortable="false"  data-visible="true"><?= $lang->_('URL') ?></th>
                            <th data-column-id="description" data-type="string" data-sortable="false"  data-visible="true"><?= $lang->_('Description') ?></th>
                            <th data-column-id="commands" data-width="7em" data-formatter="commands" data-sortable="false"><?= $lang->_('Edit | Delete') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td>
                                <button data-action="add" type="button" class="btn btn-xs btn-default"><span class="fa fa-plus"></span></button>
                                <button data-action="deleteSelected" type="button" class="btn btn-xs btn-default"><span class="fa fa-trash-o"></span></button>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    <div class="col-md-12">
                        <hr/>
                        <button class="btn btn-primary" id="reconfigureAct" type="button"><b><?= $lang->_('Apply') ?></b><i id="reconfigureAct_progress" class=""></i></button>
                        <button class="btn btn-primary" id="fetchandreconfigureAct" type="button"><b><?= $lang->_('Download ACLs & Apply') ?></b><i id="fetchandreconfigureAct_progress" class=""></i></button>
                        <button class="btn btn-primary" id="downloadAct" type="button"><b><?= $lang->_('Download ACLs') ?></b><i id="downloadAct_progress" class=""></i></button>
                        <button class="btn btn-primary" id="ScheduleAct" type="button"><b><?= $lang->_('Schedule with Cron') ?></b><i id="scheduleAct_progress" class=""></i></button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<?= $this->partial('layout_partials/base_dialog', ['fields' => $formDialogEditBlacklist, 'id' => 'DialogEditBlacklist', 'label' => 'Edit Blacklist']) ?>
