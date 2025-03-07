<p id="vkid_button"></p>
<script src="https://unpkg.com/@vkid/sdk@<2.0.0/dist-sdk/umd/index.js"></script>
<script>

    const VKID = window.VKIDSDK;

    VKID.Config.set({
        app: @vk_app@,
        redirectUrl: 'https://@vk_redirect_uri@',
        state: '@php echo urlencode($_SERVER["REQUEST_URI"]); php@'
    });

    const oneTap = new VKID.OneTap();
    const container = document.getElementById('vkid_button');
    if (container) {
        oneTap.render({container: container, scheme: VKID.Scheme.LIGHT, lang: VKID.Languages.RUS});
    }

</script>