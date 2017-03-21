/**
 * MageFlow Connector Magento extension JavaScript file
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage JavaScript
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Handle new connect button click
 */
if ($('btn_connect_mf')) {
    $('btn_connect_mf').observe('click', function (e) {
        var sender = e.findElement('button');
        var url = sender.readAttribute('data-api-url');
        var newwin = window.open('', '_top');
        new Ajax.Request(url, {
            onSuccess: function (transport) {
                var response = transport.responseJSON;
                if (response.redirectUrl != undefined) {
                    (function (url, token, callbackUrl, comehomeUrl, instanceKey, instanceType) {
                        var newLocation = url + '?token=' + token
                            + '&instanceKey=' + instanceKey
                            + '&callbackUrl=' + encodeURI(callbackUrl)
                            + '&redirectUrl=' + encodeURI(comehomeUrl)
                            + '&type=' + instanceType;
                        newwin.location.href = newLocation;
                        newwin.focus();
                    }(
                        response.redirectUrl,
                        response.token,
                        response.callbackUrl,
                        response.comehomeUrl,
                        response.instanceKey,
                        response.instanceType
                    ));
                }
            }
        });
    });
}

/**
 * Event handlers
 */
if ($('btn_apitest')) {
    $('btn_apitest').observe('click', function (e) {
        var sender = e.findElement('button');
        var url = sender.readAttribute('data-api-url');
        new Ajax.Request(url, {
            onSuccess: function (transport) {
                var response = transport.responseJSON;
                console.log(response);
                if (response.status) {
                    var className = 'notice';
                    var message = 'Connected';
                    if (response.status != 200) {
                        className = 'critical';
                        message = 'Disconnected';
                    }
                    var span = $('api_status_icon');
                    if (null == span) {
                        span = new Element('span', {id: 'api_status_icon'});
                        $('api_test_status').appendChild(span);
                        var childSpan = new Element('span');
                        span.appendChild(childSpan);
                    } else {
                        var childSpan = $('api_status_icon').down('span');
                    }
                    $('api_status_icon').className = '';
                    $('api_status_icon').addClassName('grid-severity-' + className);
                    span.setStyle({'width': '100px'});
                    $('ApiTestResults').removeClassName('no-display');

                    childSpan.innerHTML = message;
                    span.addClassName('grid-severity-' + className);

                    $('api_test_remote_mfx_version').innerHTML = '<strong>' + response.mfx_version + '</strong>';
                    $('api_test_remote_instance_key').innerHTML = '<strong>' + response.instance_key+ '</strong>';
                    $('api_test_remote_base_url').innerHTML = '<strong>' + response.base_url + '</strong>';
                } else {
                    $('api_test_status').innerHTML = 'Connection failed.';
                }
            }
        });
    });
}

/**
 * Button click that toggles monitoring via MageFlow API
 */
if ($('btn_toggle_monitor')) {
    $('btn_toggle_monitor').observe('click', function (e) {
        var sender = e.findElement('button');
        var url = sender.readAttribute('data-api-url');
        new Ajax.Request(url, {
            onSuccess: function (transport) {
                var response = transport.responseJSON;
                var statusText = (response.monitoring_enabled == 1) ? 'enabled' : 'disabled';
                var el = $('monitoring_status');
                if (null == el) {
                    el = new Element('span', {id: 'monitoring_status'});
                    sender.up().appendChild(el);
                }
                el.innerHTML = '&nbsp;&nbsp;' + statusText;
            }
        });
    });
}
var changeSetDescription = null;
document.observe("dom:loaded", function () {
    if ($('btn_apitest')) $('btn_apitest').click();
    var elementList = $$('#migrationGrid_table select.action-select');
    $A(elementList).each(function (item) {
        Element.extend(item).writeAttribute('onchange', 'javascript:;');
    });
    var container = $('migrationGrid_massaction-form');
    if (container) {
        var input = new Element('input', {
            type: 'hidden',
            id: 'migrationGrid_massaction-form-comment',
            name: 'comment'
        });
        container.appendChild(input);
    }
    if ($('migrationGrid_massaction-select') != undefined) $('migrationGrid_massaction-select').on('click', function (event, element) {
        Element.extend(element).observe('change', massActionDescription);
    });
    if ($('migrationGrid_massaction') != undefined) $('migrationGrid_massaction').on('change', function (event, element) {
        Element.extend(element).observe('change', massActionDescription);
    });

    if ($('migrationGrid') != undefined) $('migrationGrid').on('change', 'select.action-select', function (e, element) {
        var hrefValue = e.findElement('select').getValue();
        var url = hrefValue.substr(hrefValue.search('http')).replace(/\\/g, '').replace('}', '').replace('"', '');
        if (url.search('push/push') > 0) {
            changeSetDescription = prompt('Changeset description');
            if (changeSetDescription == null) {
                e.target.value = '';
                return;
            }
            url += 'comment/' + changeSetDescription;
        }
        window.location = url;
    });
});


var massActionDescription = function (e) {
    var el = e.findElement('select');
    if (migrationGridJsObject.massaction && migrationGridJsObject.massaction.checkedString == '') {
        $('migrationGrid_massaction-select').selectedIndex = 0;
        alert(migrationGridJsObject.massaction.errorText);
        return;
    }
    switch (el.getValue()) {
        case 'push':
            var hrefValue = el.getValue();
            if (hrefValue == 'push') {
                changeSetDescription = prompt('Changeset description');
                if (null != changeSetDescription) {
                    if ($('migrationGrid_massaction-form-comment') != undefined) $('migrationGrid_massaction-form-comment').setValue(changeSetDescription);
                    migrationGrid_massactionJsObject.apply();
                } else {
                    el.setValue('');
                    migrationGrid_massactionJsObject.unselectAll();
                }
            }
            break;
        case 'flush':
            migrationGrid_massactionJsObject.selectAll();
            break;
        default:
            break;
    }
};