function saferouteprodwidgetStart() {
    // ������������� �������
    new SafeRouteCardWidget("saferoute-card-widget", {
        apiScript: "/phpshop/modules/saferoutewidget/api/saferoute-widget-api.php"
    });

    $("#saferoutewidgetModal").modal("toggle");
}