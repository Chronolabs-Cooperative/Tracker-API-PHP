/**
 * 
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */


// created 2005-1-12 by Martin Sadera (sadera@e-d-a.info)
// ported to API CMS by ralf57
// updated to TinyMCE v3.0.1 / 2008-02-29 / by luciorota


(function() {
    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('apicode');

    tinymce.create('tinymce.plugins.APIcodePlugin', {
        init : function(ed, url) {
            // Register commands
            ed.addCommand('mceAPIcode', function() {
                ed.windowManager.open({
                    file : url + '/apicode.htm',
                    width : 460 + parseInt(ed.getLang('apicode.delta_width', 0)),
                    height : 300 + parseInt(ed.getLang('apicode.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register buttons
            ed.addButton('apicode', {
                title : 'apicode.code_desc',
                image : url + '/img/apicode.gif',
                cmd : 'mceAPIcode'
                });
        },

        getInfo : function() {
            return {
                longname : 'APICode',
                author : 'Martin Sadera/ralf57/luciorota',
                authorurl : '',
                infourl : '',
                version : '1.1'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('apicode', tinymce.plugins.APIcodePlugin);
})();
