<script>
    var jQ = false;
    function initJQ() {
        if (typeof(jQuery) == 'undefined') {
            if (!jQ) {
                jQ = true;
                document.write('<scr' + 'ipt type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></scr' + 'ipt>');
            }
            setTimeout('initJQ()', 50);
        } else {
            (function($) {
                $(function() {
                    if (jQ) {
                        // ������ ��������� ������ ��� �����������
                        $(".passGen").click(function() {
                            var str = wpiGenerateRandomNumber(8);
                            $(this).closest('form').find("input[name='password_new'], input[name='password_new2']").val(str);
                            alert('{��� ��������������� ������ ����� ������ ����� ���������� �����������}.');
                        });

                        // ������� ��������� ������
                        function wpiGenerateRandomNumber(limit) {

                            limit = limit || 8;

                            var password = '';

                            var chars = 'abcdefghijklmnopqrstuvwxyz0123456789';

                            var list = chars.split('');
                            var len = list.length, i = 0;

                            do {

                                i++;

                                var index = Math.floor(Math.random() * len);

                                password += list[index];

                            } while (i < limit);

                            return password;

                        }
                    }

                })
            })(jQuery)
        }
    }
    initJQ();
</script>
<div id="checkout">
    <div class="checkout-content" style="display: block;"><div class="left" style="float: left; width: 49%;">
            <form method="post" name="user_formaOrder" action="#checkout">
                <input type="hidden" name="fromSave" value="@fromSave@"/>
                <h2>{�����������}</h2>
                <p>{������� ������� ������}:</p>
                <b>E-Mail:</b> <span style="color:red">@user_error@</span><br>
                <input type="text"  name="login_new" value="@php echo $_POST['login_new']; php@">
                <br>
                <br>
                <b>{������} (<a class="passGen" href="#" onclick="return false;">{�������������}</a>):</b><br>
                <input type="password" name="password_new"  value="@php echo $_POST['password_new']; php@">
                <br>
                <br>
                <b>{��������� ������}:</b><br>
                <input type="password" name="password_new2"  value="@php echo $_POST['password_new2']; php@">
                <br>
                <br>
                <b>{���� ���}:</b><br>
                <input type="text"  name="name_new" value="@php echo $_POST['name_new']; php@">
                <br>
                <br>
                @captcha@
                <br>
                <br>
                <input type="submit" value="{�����������}" id="button-login" class="button"><br>
                <input type="hidden" value="1" name="add_user">
                <br>
                <br>
            </form>
        </div>
        <div id="login" class="right" style="float: right; width: 49%;">
            <form method="post" name="user_formaOrder" action="#checkout">
                <input type="hidden" name="fromSave" value="@fromSave@"/>
                <h2>{��������������}</h2>
                <p>{� ��� ���������������}:</p>
                <b>E-Mail:</b> <span style="color:red">@shortAuthError@</span><br>
                <input type="text"  name="login" value="@php echo $_POST['login']; php@">
                <br>
                <br>
                <b>{������}:</b><br>
                <input type="password" name="password"  value="@php echo $_POST['password']; php@">
                <br>
                <br>
                <input id="zap" type="checkbox" value="1" name="safe_users"> {��������� ����}!
                <br>
                <br>
                <a href="/users/sendpassword.html" class="forg2">{������ ������}?</a> 
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@facebookAuth@ @twitterAuth@
                <br>
                <br>
                <input type="submit" value="{�����}" id="button-login" class="button"><br>
                <input type="hidden" value="1" name="user_enter">
                <br>
                <br>
            </form>
        </div>
    </div>
</div>
