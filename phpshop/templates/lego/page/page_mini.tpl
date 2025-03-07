<div class="grid-item col-md-4 col-lg-4 col-sm-4 col-xs-6" itemscope itemtype="https://schema.org/Article">
    <meta itemprop="author" content="@company@">
    <meta itemprop="image" content="@pageIcon@">
    <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
        <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
            <img itemprop="url image" src="@logo@" alt="@company@" title="@company@" style="display:none;"/>
        </div>
        <meta itemprop="name" content="@company@">
    </div>
    <div class="news-mini-block  fadeInUp animated wow">
        <div class="img-block">
            <a itemprop="mainEntityOfPage" href="/page/@pageLink@.html" title="@pageName@"><img src="@pageIcon@"  alt=""></a>
        </div>
        <div class="text-block">
            <h2><a href="/page/@pageLink@.html" itemprop="headline" title="@pageName@">@pageName@</a></h2>
            <div class="description">
                @pagePreview@
                <span itemprop="datePublished dateModified" class="date">@pageData@</span>
            </div>
        </div>
    </div>
</div>
