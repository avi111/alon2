(function () {
    tinymce.PluginManager.add('widgets', function (editor, url) {
        "use strict";
        var widgets=window.widgets;
        for (var i = 0; i < widgets.length; i++) {
            var name = widgets[i];
            editor.addButton(name, {
                tooltip: 'Insert ' + name,
                text: name,
                icon: false,
                onclick: function (e) {
                    editor.selection.setContent('[' + e.target.innerText + ']');
                }
            });
        }
    });
})();