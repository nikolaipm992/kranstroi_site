$().ready(function() {

    // Instance the tour
    var tour = new Tour({
        storage: window.sessionStorage,
        debug: false,
        template: '<div class="popover" role="tooltip"> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation"> <div class="btn-group"> <button class="btn btn-sm btn-default" data-role="prev">&laquo; �����</button> <button class="btn btn-sm btn-default" data-role="next">����� &raquo;</button> <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">�����</button> </div> <button class="btn btn-sm btn-default" data-role="end">���������</button> </div> </div>',
        steps: [
            {
                element: ".navbar-action .navbar-brand",
                title: "��������",
                content: '���������� �� ���������� �������� ����� ��� �������� ������ ������',
                placement: 'right'
            },
            {
                element: '[name=name_new]',
                title: "��������",
                content: '������� �������� ������',
                placement: 'top'
            },
            {
                element: '[name=uid_new]',
                title: "�������",
                content: '������� ���������� ������� ������. ������� ��������� ��� ������������� ������� � ������� ����������� �� ������� �������������� ���� (1�, ��� �����)',
                placement: 'right'
            },
            {
                element: "[name=category_new]",
                title: "���������",
                content: '�������� ��������� ���������� ������ ������',
                placement: 'top'
            },

            {
                element: '[name=enabled_new]',
                title: "����� ������",
                content: '������� �����: ����� � ��������, c�������������� ��� �������',
                placement: 'top'
            },
            {
                element: '[name=price_new]',
                title: "����",
                content: '������� �������� ���� � �������������� ����. �������������� ���� ����� �������������� ��� ���������� ���������������� �������� (�������, �������). <p></p> ��������� �������� ������������� ����������� � ������� <a href="?path=shopusers.status" target="_blank">������� � ������ �����������</a>',
                placement: 'right',
                onNext: function() {
                    $('[data-id="�����������"]').tab('show');
                }

            },
            {
                element: '[data-id="�����������"]',
                title: "�����������",
                content: '�������� ������ ����� ������ ����������� ������ ��� ����������� �� �������� � ��������� (��������� �������) �� �������. �������������� <b>�������� �������� �����������</b>.<p></p> ��������� ���������� ��������� ����������� (��������� � �������) ����������� � <a href="?path=system.image" target="_blank">���������� �����������</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="��������"]').tab('show');
                }
            },
            {
                element: '[data-id="��������"]',
                title: "������� ��������",
                content: '��������� ���� �������� �������� ������, ��� ����� ��������� � ������ ������� � ��������. ������, ������� �������� �� ����� 500 ��������.<p></p> ����� ����������� ��������� ��� ������ ���������� HTML ������ ����������� � <a href="?path=system#4" target="_blank">����� ����������</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="��������"]').tab('show');
                }
            },
            {
                element: '[data-id="��������"]',
                title: "��������� ��������",
                content: '��������� ���� ���������� �������� ������, ��� ����� ��������� � ������������ �������� ������. ��������� �������� ������ �� ����� ������ �� ���������� ��������. �� ������ ��������� � �������� �������������� ���������� �������� (��������, �����).<p></p> ����� ����������� ��������� ��� ������ ���������� HTML ������ ����������� � <a href="?path=system#4" target="_blank">����� ����������</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="�������������"]').tab('show');
                }
            },
            {
                element: '[data-id="�������������"]',
                title: "��������� � ���������",
                content: '� ���� ������� ����� ���������� � ������ ������ �� �������� � ���������� � �������������� ����� (����������, ������, �����). ��� ��������� ������ ��������� �������� ������� (������� ��������), �������������� ����� ����� �������� ����������� ������ ����� ������ ������ � ����� ������ ��������. <p></p> ����� ������� �������� ������������ � <a href="?path=system" target="_blank">����� ����������</a>. <p></p> ����-��������� ���������� ��� SEO-����������� �����. ����� ������� ��������������, ��������� � ������ ������ ��������� ��� ����������� ������.<p></p> ����� ������� ��������� ����-���������� ����������� � <a href="?path=system.seo" target="_blank">SEO ����������</a>.',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="��������������"]').tab('show');
                }
            },
            {
                element: '[data-id="��������������"]',
                title: "��������������",
                content: '��� ����������� ������������� � ������� ���������� ���������� �������������� � ������ � ������� ��� ������ � ������. �������������� �� ��������� ���� �������� � ������� ��������� ���������. <p></p> �������������� ������������� � ������� <a href="?path=sort" target="_blank">��������������</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="���������"]').tab('show');
                }
            },
            {
                element: 'button[name="saveID"] > .glyphicon-floppy-saved',
                title: "����������",
                content: '���������� ������ ������ ���������� �� ������� �� ������ <kbd>������� � �������������</kbd>',
                placement: 'bottom'
            },
            {
                element: '.go2front',
                title: "���������",
                content: '����� ����� ���������� ��� �������� �������� �� �����',
                placement: 'left'
            },
            {
                element: '.setscreen',
                title: "�������� ������",
                content: '����� ��������� ��� ��������� ������ ������ ������ ����������',
                placement: 'left'
            }
            


        ], onEnd: function() {
            $('[data-id="��������"]').tab('show');
        },
    });

    // Initialize the tour
    tour.init();

    // ������ ����
    $(".presentation").on('click', function(event) {
        event.preventDefault();
        //tour.goTo(0);
        tour.restart();
    });

    if (typeof video != 'undefined') {
        tour.goTo(0);
        tour.restart();
    }

});