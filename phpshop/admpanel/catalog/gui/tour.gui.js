$().ready(function() {

    // Instance the tour
    var tour = new Tour({
        storage: window.sessionStorage,
        debug: true,
        template: '<div class="popover" role="tooltip"> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation"> <div class="btn-group"> <button class="btn btn-sm btn-default" data-role="prev">&laquo; �����</button> <button class="btn btn-sm btn-default" data-role="next">����� &raquo;</button> <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">�����</button> </div> <button class="btn btn-sm btn-default" data-role="end">���������</button> </div> </div>',
        steps: [
            {
                element: ".navbar-action .navbar-brand",
                title: "��������",
                content: '���������� �� ���������� �������� ����� ��� �������� ������ ��������',
                placement: 'right'
            },
            {
                element: "[name=category_new]",
                title: "���������",
                content: '�������� ��������� ���������� ������ ������',
                placement: 'top'
            },
            {
                element: '[name=name_new]',
                title: "������������",
                content: '������� ������������ ��������',
                placement: 'top'
            },
            {
                element: '[name=skin_enabled_new]',
                title: "����� ������",
                content: '������� ����� ������ ��������',
                placement: 'right'
            },
            {
                element: '[name=num_cow_new]',
                title: "������",
                content: '������� ���������� ������� ��� ������ �� ���� ��������. ���� ������� ����� ������������ ���������, �� ��� ����� �� ����� ����������� �������.',
                placement: 'right',
            },
            {
                element: '[name=num_new]',
                title: "����������",
                content: '������� ������� ���������� ������� � ���� ��������.',
                placement: 'right',
            },
            {
                element: '.link-thumbnail',
                title: "�����������",
                content: '������� ������ �������� ��� ���������',
                placement: 'top',
                onNext: function() {
                    $('[data-id="��������"]').tab('show');
                }

            },
            {
                element: '[data-id="��������"]',
                title: "������� ��������",
                content: '��������� ���� �������� ��������, ��� ����� ��������� � ������ ������ ������� � ��������. ������, �������� �������� �� ����� 500 ��������.<p></p> ����� ����������� ��������� ��� ������ ���������� HTML ������ ����������� � <a href="?path=system#4" target="_blank">����� ����������</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="���������"]').tab('show');
                }
            },
            {
                element: '[data-id="���������"]',
                title: "���������",
                content: '����-��������� ���������� ��� SEO-����������� �����. ����� ������� ��������������, ��������� � ������ ������ ��������� ��� ����������� ��������.<p></p> ����� ������� ��������� ����-���������� ����������� � <a href="?path=system.seo" target="_blank">SEO ����������</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="��������������"]').tab('show');
                }
            },
            {
                element: '[data-id="��������������"]',
                title: "��������������",
                content: '��� ����������� ������������� � �������� ���������� ���������� �������������� � ������ � ������� ��� ������ � ��������. �������������� �� ��������� ���� �������� � ������� ��������� ���������. <p></p> �������������� ������������� � ������� <a href="?path=sort" target="_blank">��������������</a>',
                placement: 'bottom'
            },
            {
                element: 'button[name="saveID"] > .glyphicon-floppy-saved',
                title: "����������",
                content: '���������� ������ �������� ���������� �� ������� �� ������ <kbd>������� � �������������</kbd>',
                placement: 'bottom'
            },
            {
                element: '.go2front',
                title: "���������",
                content: '����� ����� ���������� ��� �������� �������� �� �����',
                placement: 'left'
            }
        ],
        onEnd: function() {
            $('[data-id="��������"]').tab('show');
        },
    });

    // Initialize the tour
    tour.init();

    // ������ ����
    $(".presentation").on('click', function(event) {
        event.preventDefault();
        tour.goTo(0);
        tour.restart();
    });

    if (typeof video != 'undefined') {
        tour.goTo(0);
        tour.restart();
    }
});