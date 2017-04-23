

<script type="text/javascript">
    $( document ).ready(function() {
        /**
         * fetch system arp table
         */
        function updateARP() {
            var gridopt = {
                ajax: false,
                selection: false,
                multiSelect: false
            };
            $("#grid-arp").bootgrid('destroy');
            ajaxGet(url = "/api/diagnostics/interface/getArp",
                    sendData = {}, callback = function (data, status) {
                        if (status == "success") {
                            var html = [];
                            $.each(data, function (key, value) {
                                var fields = ["ip", "mac", "manufacturer", "intf", "intf_description", "hostname"];
                                tr_str = '<tr>';
                                for (var i = 0; i < fields.length; i++) {
                                    if (value[fields[i]] != null) {
                                        tr_str += '<td>' + value[fields[i]] + '</td>';
                                    } else {
                                        tr_str += '<td></td>';
                                    }
                                }
                                tr_str += '</tr>';
                                html.push(tr_str);
                            });
                            $("#grid-arp > tbody").html(html.join(''));
                        }
                        $("#grid-arp").bootgrid(gridopt);
                    }
            );
        }

        // initial fetch
        $("#refresh").click(updateARP);
        $("#refresh").click();
    });
</script>

<div class="content-box">
    <div class="content-box-main">
        <div class="table-responsive">
            <div  class="col-sm-12">
                <table id="grid-arp" class="table table-condensed table-hover table-striped table-responsive">
                    <thead>
                    <tr>
                        <th data-column-id="ip" data-type="string"  data-identifier="true"><?= $lang->_('IP') ?></th>
                        <th data-column-id="mac" data-type="string" data-identifier="true"><?= $lang->_('MAC') ?></th>
                        <th data-column-id="manufacturer" data-type="string" data-css-class="hidden-xs hidden-sm" data-header-css-class="hidden-xs hidden-sm"><?= $lang->_('Manufacturer') ?></th>
                        <th data-column-id="intf" data-type="string" data-css-class="hidden-xs hidden-sm" data-header-css-class="hidden-xs hidden-sm"><?= $lang->_('Interface') ?></th>
                        <th data-column-id="intf_description" data-type="string" data-css-class="hidden-xs hidden-sm" data-header-css-class="hidden-xs hidden-sm"><?= $lang->_('Interface name') ?></th>
                        <th data-column-id="hostname" data-type="string" data-css-class="hidden-xs hidden-sm" data-header-css-class="hidden-xs hidden-sm"><?= $lang->_('Hostname') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="6"><?= $lang->_('NOTE: Local IPv6 peers use NDP instead of ARP.') ?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div  class="col-sm-12">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="pull-right">
                            <button id="refresh" type="button" class="btn btn-default">
                                <span><?= $lang->_('Refresh') ?></span>
                                <span class="fa fa-refresh"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <hr/>
            </div>
        </div>
    </div>
</div>
