<div class="col-md-4" itemscope itemtype="https://schema.org/NewsArticle">
    <meta itemprop="author" content="@company@">
    <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
        <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
            <img itemprop="url image" src="@logo@" alt="@company@" title="@company@" style="display:none;"/>
        </div>
        <meta itemprop="name" content="@company@">
    </div>
    
    
    <div class="news-body">
        <div class="news-img  @php __hide('newsIcon'); php@ ">
            <img itemprop="image" src="@newsIcon@" alt="@newsZag@" title="@newsZag@">
        </div>
        <a itemprop="mainEntityOfPage" class="news-name" href="/news/ID_@newsId@.html" title="@newsZag@"><h4 itemprop="headline">@newsZag@</h4></a>
        <div class="news-text" itemprop="description">@newsKratko@
            <p class="news-data" itemprop="datePublished dateModified">@newsData@</p>
        </div>
    </div>
</div>