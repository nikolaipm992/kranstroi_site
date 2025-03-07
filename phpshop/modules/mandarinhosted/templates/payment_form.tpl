<style>.hosted-field{background:#f0f0f0;height:40px;padding:5px;border:1px solid gray;border-radius:10px;position:relative}.hosted-field .glyphicon{visibility:hidden;position:absolute;right:5px;top:5px;color:green;float:right}.mandarinpay-field-state-error{background:#fff0f0;border:1px solid #900000}.mandarinpay-field-state-focused{background:#fff;border:1px solid #9acd32}.mandarinpay-field-state-valid{background:#c0ffc0!important;border:1px solid green!important}.mandarinpay-field-state-valid .glyphicon{visibility:visible}</style>
@payment_info@
<form id="form-hosted-pay">
	<div style="margin: 10px; padding: 10px; border: 1px solid gray">
		Card Number:
		<div class="mandarinpay-field-card-number hosted-field"><div class="glyphicon glyphicon-check"></div></div>
		Card Holder:
		<div class="mandarinpay-field-card-holder hosted-field"><div class="glyphicon glyphicon-check"></div></div>
		Card Expiration:
		<div class="mandarinpay-field-card-expiration hosted-field"><div class="glyphicon glyphicon-check"></div></div>
		CVV:
		<div class="mandarinpay-field-card-cvv hosted-field"><div class="glyphicon glyphicon-check"></div></div>
		<br/>
		<a href="#" onclick="return mandarinpay.hosted.process(this);" class="btn btn-default">��������</a>
	</div>
</form>

<script src="https://secure.mandarinpay.com/api/hosted.js"></script>
<script>
mandarinpay.hosted.setup("#form-hosted-pay",{
  operationId: "@operationId@",
  onsuccess: function(data) {
    window.location.href = "/success/?payment=mandarinhosted";
  },
  onerror: function(data) {
    window.location.href = "/fail/";
  }
});
</script>
