/**
 * @author          ralf57
 * @author          luciorota (lucio.rota@gmail.com)
 * @author          dugris (dugris@frapi.fr)
 */

(function() {
    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('apiimagemanager');

    tinymce.create('tinymce.plugins.APIimagemanagerPlugin', {
        init : function(ed, url)
        {
            // Register commands
            ed.addCommand('mceAPIimagemanager', function() {
                var e = ed.selection.getNode();

                // Internal image object like a flash placeholder
                if (ed.dom.getAttrib(e, 'class').indexOf('mceItem') != -1)
                    return;

                ed.windowManager.open({
                    file : url + '/apiimagemanager.php',
                    width : 480 + parseInt(ed.getLang('apiimagemanager.delta_width', 0)),
                    height : 385 + parseInt(ed.getLang('apiimagemanager.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register buttons
            ed.addButton('apiimagemanager', {
                title : 'apiimagemanager.desc',
                cmd : 'mceAPIimagemanager',
                image : url + '/img/apiimagemanager.png'
            });

            // Add a node change handler, selects the button in the UI when a image is selected
            ed.onNodeChange.add(function(ed, cm, n) {
                cm.setActive('apiimagemanager', n.nodeName == 'IMG');
            });
        },

        getInfo : function()
        {
            return {
                longname : 'API Advanced Image Manager',
                author : 'luciorota (lucio.rota@gmail.com) / dugris (dugris@frapi.fr)',
                version : "1.1"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('apiimagemanager', tinymce.plugins.APIimagemanagerPlugin);
})();