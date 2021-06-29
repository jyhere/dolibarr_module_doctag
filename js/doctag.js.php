<?php
if (!defined('NOTOKENRENEWAL')) {
    define('NOTOKENRENEWAL', 1);
}

if(is_file('../../master.inc.php')) {
    require('../../master.inc.php');
}
else {
    require('../../../master.inc.php');
}

$langs->load('doctag@doctag');
//echo $langs->trans('PreviewOf');
?>

function docTag_set_link() {
    $('.doctag-link').remove();

    <?php if ((float)DOL_VERSION >= 5) : ?>
    $("a.documentpreview[href],a.pictopreview[href]")
    <?php else : ?>
    $("a[href]")
    <?php endif; ?>
    .each(function () {
        var $a = $(this);
        var url = $a.attr('href');

        if (url.indexOf('document.php?') != -1 && url.indexOf('action=delete') == -1 && url.indexOf('file=') != -1) {
            filename = $a.text();
            if (filename == '') {
                filename = $a.find('img').attr('alt');
            }
            var tag64 = window.btoa(url);
            url = "javascript:docTag_pop('<?= dol_buildpath('/doctag/tag.php', 1) ?>?tag64=" + tag64 + "','" + filename + "')";
            if ($a.hasClass('pictopreview') && !$a.hasClass('documentpreview')) {
                link = '<br /><a class="doctag-link" href="' + url + '" tag64="' + tag64 + '"><?= img_object($langs->trans('Tagit'), 'doctag@doctag') . ' ' . $langs->trans('tag'); ?></a>';
            } else if ($a.parent().is('li')) {
                link = '';
            } else {
                link = '&nbsp;<a class="doctag-link" href="' + url + '" tag64="' + tag64 + '"><?= img_object($langs->trans('Tagit'), 'doctag@doctag') ?></a>';
            }

            if (link.length > 0) {

                $(this).after(link);

                $.ajax({
                    url: "<?= dol_buildpath('/doctag/script/interface.php?get=tag64exist&tag64=', 1) ?>" + tag64,
                    global: false
                }).done(function (nb_tag) {
                    if (nb_tag > 0) {
                        $img = $('a[tag64="' + tag64 + '"]').find("img");
                        $img.attr("src", "<?= img_picto('', 'object_doctag_blue@doctag', '', false, 1, 1) ?>");
                        $img.attr("title", nb_tag + " tag(s)");
                    }
                });
            }
        }
    });
}

function docTag_pop(url, filename) {

    $('#docTag').remove();

    if($('#docTag').length==0) {
        $('body').append('<div id="docTag"><iframe src="#" width="100%" height="100%" allowfullscreen webkitallowfullscreen frameborder="0"></iframe></div>');
    }

    $('#docTag').dialog({
        title: "<?php echo $langs->trans('TagsOfThis') ?> "
        ,resize:'auto'
        ,width:800
        ,height:500
        ,modal:true
        ,resizable: true
        ,close:function() {
            $('#docTag iframe').attr('src', '#');
        }
    });

    $('#docTag iframe').attr('src', url);

}

$(document).ready(function() {
    docTag_set_link();
});

$(document).ajaxComplete(function() {
    docTag_set_link();
});
