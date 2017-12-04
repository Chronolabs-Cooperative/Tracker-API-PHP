/**
 * @author          ralf57
 * @author          luciorota (lucio.rota@gmail.com)
 * @author          dugris (dugris@frapi.fr)
 */

tinyMCEPopup.requireLangPack();

var APImlcontentDialog = {
    init : function()
    {
        tinyMCEPopup.resizeToInnerSize();

        var f = document.forms[0];
        // Get the selected contents as text and place it in the input
        text = tinyMCEPopup.editor.selection.getContent({format : 'text'});
        f.mltext.value = text.replace(/\[(.*?)\](.*?)\[\/(.*?)\]/ig, "$2");
        f.mlanguages.value = text.replace(/\[(.*?)\](.*?)\[\/(.*?)\]/ig, "$1");
        APImlcontentDialog.onkeyupMLC(this);
    },

    insertMLC : function()
    {
        var f = document.forms[0];

        var mltext = f.mltext.value;
        var selectlang = f.mlanguages.value;
        if ( selectlang != '' ) {
            if ( mltext != '' ) {
                mltext.replace(new RegExp("<",'g'), "&lt;");
                mltext.replace(new RegExp(">",'g'), "&gt;");
                var html = '['+selectlang+']';
                html += mltext+'[/'+selectlang+']';

                // Insert the contents from the input into the document
                tinyMCEPopup.editor.execCommand('mceInsertContent', true, html);
            }
            tinyMCEPopup.close();
        } else if (selectlang == '' && mltext != '') {
            alert( tinyMCEPopup.getLang('apimlcontent_dlg.chooselang') );
        } else {
            tinyMCEPopup.close();
        }
    },

    // limit to 10000 caracters to prevent preg_replace bug
    onkeyupMLC : function()
    {
        var f = document.forms[0];
        var str = new String(f.mltext.value);
        var len = str.length;
        var maxKeys = 10000;

        if ( len > maxKeys ) {
            alert( tinyMCEPopup.getLang('apimlcontent_dlg.alertmaxstring') );
            f.mltext.value = str.substr(0, maxKeys);
            var str = new String(f.mltext.value);
            var len = str.length;
        }

        var maxText = tinyMCEPopup.getLang('apimlcontent_dlg.maxstring');
        maxText = len + maxText.replace('%maxchar%', maxKeys);
        document.getElementById("mltext_msg").innerHTML = maxText;

    }
}

tinyMCEPopup.onInit.add(APImlcontentDialog.init, APImlcontentDialog);