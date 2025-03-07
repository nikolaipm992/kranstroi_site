
<li class="list-inline-item nav-item  dropdown ">
    <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-user-circle"></i>
    </a>

        <!-- Navbar Vertical -->
        <div class="dropdown-menu navbar-vertical dropdown-menu-right" aria-labelledby="navbarDropdown" style="width:200px">
            <!-- Card -->
            <div class="card">
                <div class="card-body">
                    <h6 class="text-cap small">{�������}</h6>

                    <!-- List -->
                    <ul class="nav nav-sub nav-sm nav-tabs nav-list-y-2 mb-4">
                        <li class="nav-item">
                            <a class="nav-link" href="/users/">
                                <i class="fas fa-id-card nav-icon"></i>
                                {���������}
                            </a>
                        </li>
                        <li class="nav-item @hideCatalog@">
                            <a class="nav-link" href="/users/order.html">
                                <i class="fas fa-shopping-basket nav-icon"></i>
                                {������}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/users/message.html">
                                <i class="fas fa-comments nav-icon"></i>
                                {�������}
                            </a>
                        </li>
                        <li class="nav-item @hideCatalog@">
                            <a class="nav-link" href="/users/notice.html">
                                <i class="fas fa-bell nav-icon"></i>
                                {�����������}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?logout=true">
                                <i class="fas fa-sign-out-alt nav-icon"></i>
                                {�����}
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->

                    <h6 class="text-cap small @hideSite@">{������}</h6>

                    <!-- List -->
                    <ul class="nav nav-sub nav-sm nav-tabs nav-list-y-2 mb-2">
                        <li class="nav-item @hideCatalog@">
                            <a class="nav-link" href="/order/">
                                <i class="fas fa-shopping-cart nav-icon"></i>
                                {�������}
                                <span class="badge badge-soft-dark badge-pill nav-link-badge cartnum">@num@</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/compare/">
                                <i class="fas fa-cogs nav-icon"></i>
                                {��������}
                                <span class="badge badge-soft-dark badge-pill nav-link-badge numcompare">@numcompare@</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/users/wishlist.html">
                                <i class="fas fa-heart nav-icon"></i>
                                {���������}
                                <span class="badge badge-soft-dark badge-pill nav-link-badge wishlistcount">@wishlistCount@</span>
                            </a>
                        </li>
                    </ul>
                    <!-- End List -->

                </div>
            </div>
            <!-- End Card -->
        </div>
        <!-- End Navbar Vertical -->
</li>
