<style>.brands{display:none;}</style>   
<footer class="footer footer-3" id="footer-area">
    <!-- Footer Links Starts -->
    <div class="footer-links">
        <!-- Container Starts -->
        <div class="container-fluid">
            <!-- Contact Us Starts -->
            <div class="col-md-3 col-sm-6 col-xs-12" itemscope itemtype="http://schema.org/Organization">
                <a href="/" class="@php __hide('logo'); php@"><img src="@logo@" alt="" width="180"></a>
                <ul>
                    <li class="footer-map" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="streetAddress">@streetAddress@</span></li>
                    <li class="footer-email"><a href="mailto:@adminMail@" itemprop="email">@adminMail@</a></li>
                    <li class="footer-map" itemprop="telephone">@telNum@</li>
                    <li class="footer-map" itemprop="telephone">@telNum2@</li>
                    <li class="footer-map">@workingTime@</li>
                </ul>
            </div>


            <!-- Contact Us Ends -->
            <!-- My Account Links Starts -->
            <div class="col-md-3  col-sm-6 col-xs-12">
                <ul>
                    <li><a href="/users/">@UsersLogin@</a></li>
                    <li class="@hideCatalog@"><a href="/users/order.html">{Отследить заказ}</a></li>
                    <li class="@hideSite@"><a href="/users/wishlist/">{Отложенные товары}</a></li>
                    <li><a href="/users/message.html">{Связь с менеджерами}</a></li>
                    @php if($_SESSION['UsersId']) echo '
                    <li><a href="?logout=true">{Выйти}</a></li>
                    '; else echo '<li><a href="#" data-toggle="modal" data-target="#userModal">{Войти}</a></li>'; php@
                </ul>
            </div>
            <!-- My Account Links Ends -->
            <div class="clearfix visible-sm"></div>
            <!-- Customer Service Links Starts -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <ul>
                    @bottomMenu@

                </ul>
            </div>
            <!-- Customer Service Links Ends -->
            <!-- Information Links Starts -->
            <div class="col-md-3 col-sm-6 col-xs-12 t-right">

                @sticker_pay@

                <!-- Социальные сети -->
                <ul class="social-menu list-inline">
                    <li class="list-inline-item @php __hide('vk'); php@"><a class="social-button header-top-link" title="ВКонтакте" href="@vk@" target="_blank"><em class="fa fa-vk" aria-hidden="true">.</em></a></li>
                    <li class="list-inline-item @php __hide('telegram'); php@"><a class="social-button header-top-link" title="Telegram" href="@telegram@" target="_blank"> <em class="fa fa-telegram" aria-hidden="true">.</em></a></li>
                    <li class="list-inline-item @php __hide('odnoklassniki'); php@"><a class="social-button header-top-link" title="Одноклассники" href="@odnoklassniki@" target="_blank"> <em class="fa fa-odnoklassniki" aria-hidden="true">.</em></a></li>
                    <li class="list-inline-item @php __hide('youtube'); php@"><a class="social-button header-top-link" title="Youtube" href="@youtube@" target="_blank"><em class="fa fa-youtube" aria-hidden="true">.</em></a></li>
                    <li class="list-inline-item  @php __hide('whatsapp'); php@"><a class="social-button header-top-link" title="WhatsApp" href="@whatsapp@" target="_blank"><em class="fa fa-whatsapp" aria-hidden="true">.</em></a></li>
                </ul>
                <!-- / Социальные сети -->

                @button@
            </div>
            <!-- Information Links Ends -->
        </div>

        <!-- Container Ends -->
    </div>
    <!-- Footer Links Ends -->

</footer>