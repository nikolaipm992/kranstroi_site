<?php
session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "yandexcloud", "orm", "date", "security", "string", "parser", "lang", "admgui"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, false);
$PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();
$PHPShopGUI = new PHPShopGUI();

$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// ���� ����������
if (empty($_SESSION['admin_theme']))
    $theme = PHPShopSecurity::TotalClean($PHPShopSystem->getSerilizeParam('admoption.theme'));
else
    $theme = $_SESSION['admin_theme'];
if (!file_exists('../css/bootstrap-theme-' . $theme . '.css'))
    $theme = 'default';
?>

<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['PHPShopLang']->code; ?>">
    <head>
        <meta charset="<?php echo $GLOBALS['PHPShopLang']->charset; ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>YandexGPT</title>

        <!-- Bootstrap -->
        <link  href="../../css/bootstrap-theme-<?php echo $theme; ?>.css" rel="stylesheet">
        <link  href="../../css/admin.css" rel="stylesheet">
        <link href="../../css/messagebox.min.css" rel="stylesheet">
        <link href="../../css/bootstrap-toggle.min.css" rel="stylesheet">

    </head>
    <body role="document">

        <script src="../../../locale/<?php echo $_SESSION['lang']; ?>/gui.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="../../js/jquery-1.11.0.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="../../js/bootstrap.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="../../js/messagebox.min.js" data-rocketoptimized="false" data-cfasync="false"></script>
        <script src="../../js/bootstrap-toggle.js" data-rocketoptimized="false" data-cfasync="false"></script>

        <div class="content">

            <?php
            $PHPShopGUI->field_col = 3;
            $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('�����-����� YandexGPT', $PHPShopGUI->setField('����������', $PHPShopGUI->setTextarea('role', null, false, false, 70, false, "������� ����������") . $PHPShopGUI->setHelp('������� ������� ���������� �������, ��������� ����������� ��� ������� ����� ������.')) .
                    $PHPShopGUI->setField('������', $PHPShopGUI->setTextarea('user', null, false, false, 150, false, "������� ������") . $PHPShopGUI->setHelp('������������� ���� ������. ��� ����� ���� �������� �����, ���������� �������, ������.') . $PHPShopGUI->setCheckbox('html', 1, 'HTML ��������', 0)) .
                    $PHPShopGUI->setField('����� YandexGPT', $PHPShopGUI->setTextarea('result', null, false, false, 150, false) .
                            $PHPShopGUI->setButton('����������� �����', 'copy', 'ai-result-copy') . $PHPShopGUI->setButton('������ �����', 'refresh', 'ai-result-refresh'))
            );


            $PHPShopGUI->Compile(true);
            ?>

            <div class="pull-right" style="padding:10px">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?php _e('��������') ?></button>
                <button type="button" class="btn btn-primary btn-sm ai-promt"><?php _e('������ �����') ?></button>
            </div>

        </div>

        <script>
            $().ready(function () {

                $('[name="result"]').attr('readonly', 'readonly').css('background-color', '#FFF');

                // Preloader
                $('.main').removeClass('transition');

                // ���������� 
                $(".ai-result-copy").on('click', function (event) {
                    event.preventDefault();

                    if ($('[name="result"]').val() != "") {

                        var $tmp = $("<textarea>");
                        $("body").append($tmp);
                        $tmp.val($('[name="result"]').val()).select();
                        document.execCommand("copy");
                        $tmp.remove();

                        $.MessageBox({
                            buttonDone: locale.close,
                            message: locale.copy
                        });


                    } else
                        alert(locale.select_no);
                });

                // ��������
                $('body').on('click', '[data-dismiss="modal"]', function () {
                    parent.window.$('#adminModal').modal('hide');
                    parent.window.is_change = false;
                });

                // AI
                $("body").on('click', ".ai-promt, .ai-result-refresh", function () {

                    var text = $('[name="user"]').val();
                    var role = $('[name="role"]').val();

                    if ($('#html').prop('checked') === true) {
                        var html = 1;
                        var length = 1000;
                    } else {
                        html = 0;
                        var length = 300;
                    }

                    $.MessageBox({
                        buttonDone: "OK",
                        buttonFail: locale.cancel,
                        message: locale.confirm_ai_help
                    }).done(function () {

                        var data = [];
                        data.push({name: 'text', value: text});
                        data.push({name: 'length', value: length});
                        data.push({name: 'role', value: role});
                        data.push({name: 'html', value: html});

                        $.ajax({
                            mimeType: 'text/html; charset=' + locale.charset,
                            url: './gpt.ajax.php',
                            data: data,
                            type: 'post',
                            dataType: "json",
                            async: false,
                            success: function (json) {
                                if (json['success'] == 1) {

                                    $.MessageBox({
                                        buttonDone: locale.ai_done,
                                        buttonFail: locale.cancel,
                                        message: json['text'],
                                        width: "50%"
                                    }).done(function () {
                                        $('[name="result"]').val(json['text']);
                                        parent.window.is_change = false;
                                        $('[name="result"]').removeAttr('readonly');
                                    })

                                } else {
                                    $.MessageBox({
                                        buttonDone: "OK",
                                        buttonFail: locale.cancel,
                                        message: locale.ai_false
                                    })
                                }
                            }
                        });
                    })
                });

            });

        </script>
    </body>
</body>
</html>