<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="windows-1251">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>@title@ - @server@</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <style>
            html {
                position: relative;
                min-height: 100%;
            }
            body {
                margin-bottom: 60px;
            }
            .footer {
                position: absolute;
                bottom: 0;
                width: 100%;
                height: 60px;
                background-color: #f5f5f5;
            }
            .container {
                width: auto;
                max-width: 680px;
                padding: 0 15px;
            }
            .container .text-muted {
                margin: 20px 0;
            }
            a .glyphicon{
                padding-right: 3px;
            }
        </style>
    </head>
    <body role="document">
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <div class="container">
            <div class="page-header">
                <h1 class="text-danger"><span class="glyphicon glyphicon-exclamation-sign"></span> @title@</h1>
            </div>

            <p>��������� ���������� �� ���������� ������ ��� ���������� � <a class="btn btn-info btn-xs"href="https://help.phpshop.ru" target="_blank" title="����������� ���������"><span class="glyphicon glyphicon-user"></span>����������� ���������</a></p>

            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-alert"></span> ��������� �������</h3>
                </div>
                <div class="panel-body">

                    <ol>
                        <li>������ �������� PHPShop ���������� �� ����� <mark>@server@</mark>, �������� �� ���������� � ����� �������� <mark>@file@</mark>.</li>
                        <li>����� ���� �������� �������� (�������� ������ 14 ���� @date@).</li>
                        <li>�������� ��������� � ������ �������� PHPShop, �������� �� ��������� � ��������.</li>
                        <li>�������� ����������� ��������.</li>
                        <li>��������� ������������ ���������� �������������� ������ ��������.</li>
                    </ol>

                    <h4>������� ��������</h4>
                    <ol>
                        <li>��������� �������� �� ����� ����� <a href="https://www.phpshop.ru/order/?from=@server@">����� �������� ��������</a>.</li>
                        <li>������� ���� �������� <mark>@file@</mark>.</li>
                        <li>������������ � <a href="https://help.phpshop.ru/knowledgebase/article/291">����������� ������� ������� � ���������</a></li>
                        <li>�������� <a href="https://www.phpshop.ru/order/?from=@server@#buy">����� �� �������</a> ���������� �������� PHPShop.</li>
                        <li>�������� <a href="https://www.phpshop.ru/order/?from=@server@#showcase">����� �� �������</a> �������� ��� �������������� ������ PHPShop.</li>
                        <li>�������� <a href="https://www.phpshop.ru/order/?from=@server@#rent">����� �� ������</a> �������� PHPShop �� ����� ��������</li>
                        <li>�������� <a href="https://www.phpshop.ru/order/?from=@server@">����������� ���������</a> � �������� ����� �������� ��� PHPShop.</li>
                        </li>
                    </ol>

                </div>
            </div>

            <div class="text-muted">������� �� �������� ������������ ���������� ������������� ����� <span id="time">30</span> ������.</div>

        </div>
        <footer class="footer">
            <div class="container">
                <p class="text-muted text-center">
                    ������� <a href="https://www.phpshop.ru" target="_blank" title="�����������"><span class="glyphicon glyphicon-home"></span>PHPShop</a> ��� ��������������� <a href="https://help.phpshop.ru" target="_blank" title="����������� ���������"><span class="glyphicon glyphicon-user"></span>����������� ����������</a>
                </p>
            </div>
        </footer>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script>
            var countdown = $('#time'), timer;
            function startCountdown() {
                var startFrom = $('#time').text();
                timer = setInterval(function() {
                    countdown.text(--startFrom);
                    if (startFrom <= 0) {
                        clearInterval(timer);
                        window.location.replace('https://www.phpshop.ru/docs/error.html?SERVER_NAME=@server@');
                    }
                }, 1000);
            }
            $(document).ready(function() {
                startCountdown();
            });
        </script>
    </body>
</html>