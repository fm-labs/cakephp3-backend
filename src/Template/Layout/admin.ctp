<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <title><?= $this->fetch('title') ?></title>
    <meta name="description" content="">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">

    <?= $this->Html->meta('icon') ?>
    <?= $this->fetch('meta') ?>


    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,700,300&subset=latin,vietnamese' rel='stylesheet' type='text/css'>
    <?= $this->Html->css('SemanticUi.semantic.min'); ?>
    <?= $this->Html->css('Backend.admin'); ?>
    <?= $this->fetch('css') ?>


    <?= $this->fetch('script') ?>

</head>
<body class="sidebar">

<?= $this->element('Backend.navbar'); ?>
<?= $this->element('Backend.sidebar'); ?>


<div id="page">

    <div id="page-breadcrumbs">
        <?= $this->element('Backend.breadcrumbs'); ?>
    </div>

    <div id="page-main">
        <div id="page-flash">
            <?= $this->Flash->render('backend') ?>
        </div>
        <div class="ui hidden divider"></div>
        <div id="page-content">
            <?= $this->fetch('content'); ?>
        </div>
    </div>


</div> <!-- #page -->
<?= $this->Html->script('Backend.jquery-1.11.2.min.js'); ?>
<?= $this->Html->script('SemanticUi.semantic.min.js'); ?>
<?= $this->Html->script('Backend.tinymce/tinymce.min.js'); ?>
<?= $this->Html->script('Backend.tinymce/jquery.tinymce.min.js'); ?>
<script>
$(document).ready(function() {
    var $sidebar = $('#backend-admin-sidebar');

    // dropdown menus
    $('.ui.dropdown')
        .dropdown()
    ;

    // sidebar
    $sidebar.sidebar({
        transition: 'overlay',
        dimPage: false,
        onVisible: function() {
            $('body').addClass('sidebar');
        },
        onHide: function() {
            $('body').removeClass('sidebar');
            $('body').addClass('sidebar-hidden');
        },
        onHidden: function() {
            $('body').removeClass('sidebar-hidden');
        }
    });

    // sidebar toggle
    $('#backend-admin-sidebar-toggle').click(function() {
        $sidebar.sidebar('toggle');
    });

    // flash messages
    $('.message .close').on('click', function() {
        $(this).closest('.message').fadeOut();
    });

    $('.tinymce').tinymce({
        plugins: 'image link lists code table media paste wordcount',
        content_css: '<?= $this->Url->build('/'); ?>backend/css/admin.tinymce.css',
        menu : { // this is the complete default configuration
            file   : {title : 'File'  , items : 'newdocument'},
            edit   : {title : 'Edit'  , items : 'undo redo | cut copy paste pastetext | selectall'},
            insert : {title : 'Insert', items : 'link media | template hr'},
            view   : {title : 'View'  , items : 'visualaid'},
            format : {title : 'Format', items : 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
            table  : {title : 'Table' , items : 'inserttable tableprops deletetable | cell row column'},
            tools  : {title : 'Tools' , items : 'spellchecker code'}
        },
        menubar: false,
        toolbar: [
            "formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote | code",
            "undo redo | cut copy paste | link image media | table"
        ]
    });

    $('.tinymce-default').tinymce({
        content_css: '<?= $this->Url->build('/'); ?>backend/css/admin.tinymce.css'
    });
});

</script>

</body>
</html>