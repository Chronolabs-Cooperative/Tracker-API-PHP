/**
 * @author          ralf57
 * @author          luciorota (lucio.rota@gmail.com)
 * @author          dugris (dugris@frapi.fr)
 */

(function() {
    // Load plugin specific language pack
    tinymce.PluginManager.requireLangPack('apimlcontent');

    tinymce.create('tinymce.plugins.APImlcontentPlugin', {

        init : function(ed, url)
        {
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
            ed.addCommand('mceAPImlcontent', function() {
                ed.windowManager.open({
                    file : url + '/apimlcontent.php',
                    width : 520 + parseInt(ed.getLang('apimlcontent.delta_width', 0)),
                    height : 350 + parseInt(ed.getLang('apimlcontent.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url, // Plugin absolute URL
                    some_custom_arg : 'custom arg' // Custom argument
                });
            });

            // Register example button
            ed.addButton('apimlcontent', {
                title : 'apimlcontent.desc',
                cmd : 'mceAPImlcontent',
                image : url + '/img/apimlcontent.png'
            });
        },

        getInfo : function() {
            return {
                longname : 'API Multilaguage Content plugin',
                author : 'ralf57 / luciorota (lucio.rota@gmail.com) / dugris (dugris@frapi.fr)',
                version : "1.1"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('apimlcontent', tinymce.plugins.APImlcontentPlugin);
})();